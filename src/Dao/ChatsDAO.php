<?php

namespace Dao;

use Vo\Chat;
use Vo\User;
use Utils\Utils;

class ChatsDAO
{
    public function __construct()
    {
    }

    public function get($idUser)
    {
        $con = Null;
        try {
            $con = Conexao::getConexao();
            $pst = $con->prepare(
              'SELECT
              	a.id_chat, a.id_user, b.name
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

            $chats = array();
            foreach ($pst->fetchAll() as $atual) {
                $chat = new Chat();
                $chat->id = $atual['id_chat'];

                $user = new User();
                $user->id = $atual['id_user'];
                $user->name = $atual['name'];
                $chat->user = Utils::clean($user);

                $chats[] = Utils::clean($chat);
            }

            return $chats;
        } catch (\Exception $err) {
            throw $err;
        } finally {
            unset($con);
        }
    }

    /*
    public function post(User $user)
    {
        $con;
        try {
            $con = Conexao::getConexao();
            $con->beginTransaction();
            $pst = $con->prepare(
            'INSERT INTO users(
                        name, username, password, birthday, interest_from, interest_to,
                        about_me, congregation, id_genre, profession, location, radius)
                VALUES (?, ?, ?, ?, ?, ?,
                        ?, ?, ?, ?, ?, ?);');

            $pst->bindParam(1, $user->name);
            $pst->bindParam(2, $user->username);
            $pst->bindParam(3, $user->password);
            $pst->bindParam(4, $user->birthday);
            $pst->bindParam(5, $user->interestFrom);
            $pst->bindParam(6, $user->interestTo);
            $pst->bindParam(7, $user->aboutMe);
            $pst->bindParam(8, $user->congregation);
            $pst->bindParam(9, $user->genre['id']);
            $pst->bindParam(10, $user->profession);
            $pst->bindParam(11, $user->location);
            $pst->bindParam(12, $user->radius);
            $pst->execute();

            $user->id = $con->lastInsertId();
            $con->commit();

            return $user;
        } catch (\PDOException $err) {
            if (($con instanceof \PDO) && ($con->inTransaction())) {
                $con->rollBack();
            }

            throw $err;
        } finally {
            unset($con);
        }
    }

    public function put(User $user)
    {
        $con;
        try {
            $con = Conexao::getConexao();
            $con->beginTransaction();
            $pst = $con->prepare(
              'UPDATE users SET
                name=?,
                username=?,
                password=?,
                birthday=?,
                interest_from=?,
                interest_to=?,
                about_me=?,
                congregation=?,
                id_genre=?,
                profession=?,
                location=?,
                radius=?
              WHERE
                id_user = ?;'
            );

            $pst->bindParam(1, $user->name);
            $pst->bindParam(2, $user->username);
            $pst->bindParam(3, $user->password);
            $pst->bindParam(4, $user->birthday);
            $pst->bindParam(5, $user->interestFrom);
            $pst->bindParam(6, $user->interestTo);
            $pst->bindParam(7, $user->aboutMe);
            $pst->bindParam(8, $user->congregation);
            $pst->bindParam(9, $user->genre['id']);
            $pst->bindParam(10, $user->profession);
            $pst->bindParam(11, $user->location);
            $pst->bindParam(12, $user->radius);
            $pst->bindParam(13, $user->id, \PDO::PARAM_INT);
            $pst->execute();

            $con->commit();

            return $user;
        } catch (\PDOException $err) {
            if (($con instanceof \PDO) && ($con->inTransaction())) {
                $con->rollBack();
            }

            throw $err;
        } finally {
            unset($con);
        }
    }

    public function delete($id)
    {
        $con;
        try {
            $con = Conexao::getConexao();
            $con->beginTransaction();
            $pst = $con->prepare('DELETE FROM users WHERE id_user = ?;');

            $pst->bindParam(1, $id);
            $pst->execute();

            $con->commit();
        } catch (\PDOException $err) {
            if (($con instanceof \PDO) && ($con->inTransaction())) {
                $con->rollBack();
            }

            throw $err;
        } finally {
            unset($con);
        }
    }
    */
}
