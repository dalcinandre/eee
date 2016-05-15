<?php

namespace Core\Dao;

use Core\Vo\Chat;
use Core\Vo\User;

class ChatsDAO
{
    public function __construct()
    {
    }

    public function get($idUser)
    {
        $con = null;
        try {
            $con = Conexao::getConexao();
            $pst = $con->prepare(
              'SELECT
              	a.id_chat,
                a.id_user,
                b.name,
                b.last_name
              FROM
              (
              	SELECT
              		c.id_chat,
              		c.id_user_b AS id_user
              	FROM
              		chats c
              	WHERE
              		c.id_user_a = ? AND c.id_user_b != ?

              	UNION

              	SELECT
              		c.id_chat,
              		c.id_user_a
              	FROM
              		chats c
              	WHERE
              		c.id_user_a != ? AND c.id_user_b = ?
              ) a
              JOIN
              	users b USING (id_user);'
            );
            $pst->bindParam(1, $idUser);
            $pst->bindParam(2, $idUser);
            $pst->bindParam(3, $idUser);
            $pst->bindParam(4, $idUser);
            $pst->execute();

            $ret = $pst->fetchAll();
            $pst->closeCursor();
            unset($pst);
            $chats = array();

            $isOpen = false;
            foreach ($ret as $atual) {
                $chat = new Chat();
                $chat->id = $atual['id_chat'];

                $user = new User();
                $user->id = $atual['id_user'];
                $user->name = $atual['name'];
                $user->lastName = $atual['last_name'];

                if (!$pst instanceof \PDOStatement) {
                    $pst = $con->prepare(
                      'SELECT
                      	a.id_user,
                      	a.photo,
                      	a.perfil,
                      	b.id_photo
                      FROM
                      	users_photos AS a
                      JOIN
                      (
                      	SELECT
                      		max(a.id_photo) AS id_photo,
                      		a.id_user
                      	FROM
                      		users_photos AS a
                      	WHERE
                      		a.perfil IS TRUE
                      	GROUP BY
                      		a.id_user
                      ) b USING (id_user, id_photo)
                      WHERE
                      	a.id_user = ?;'
                    );
                }

                $pst->bindParam(1, $user->id);
                $pst->setFetchMode(\PDO::FETCH_CLASS, '\Core\Vo\Photo');
                $isOpen = $pst->execute();

                $user->photos = $pst->fetchAll();

                $chat->user = $user;

                $chats[] = $chat;
            }

            return $chats;
        } catch (\Exception $err) {
            throw $err;
        } finally {
            unset($con);
        }
    }

    public function delete($id, $idDislike)
    {
        $con;
        try {
            $con = Conexao::getConexao();
            $con->beginTransaction();
            $pst = $con->prepare(
            'WITH deleted AS (DELETE FROM chats WHERE id_chat = ? RETURNING ? AS id_user)
            UPDATE
            	likes AS a
            SET
            	liked = false
            FROM
            	deleted AS b
            WHERE
            	a.id_user = b.id_user;'
            );

            $pst->bindParam(1, $id);
            $pst->bindParam(2, $idDislike);
            $pst->execute();
            $pst->closeCursor();
            unset($pst);

            $con->commit();
        } catch (\Exception $err) {
            if (($con instanceof \PDO) && ($con->inTransaction())) {
                $con->rollBack();
            }

            throw $err;
        } finally {
            unset($con);
        }
    }
}
