<?php

namespace Core\Dao;

use Core\Vo\Like;
use Core\Vo\User;
use Core\Vo\Photo;
use Core\Utils\Utils;

class LikesDAO
{
    public function __construct()
    {
    }

    public function retrieve($id, $limit, $offset)
    {
        /*$con;
        try {
            $con = Conexao::getConexao();
            $pst = $con->prepare(
              'SELECT
              	b.id_user AS id,
              	b.name,
              	b.last_name AS lastName,
              	b.birthday,
              	b.bio
              FROM
              	likes AS a
              JOIN
              	users AS b on a.id_like = b.id_user
              WHERE
              	a.id_user = ?
              LIMIT
              	?
              OFFSET
              	?;'
            );

            $limit = empty($limit) ? $limit = 10 : $limit;
            $offset = empty($offset) ? $offset = 0 : $offset;

            $pst->bindParam(1, $id);
            $pst->bindParam(2, $limit);
            $pst->bindParam(3, $offset);

            $pst->setFetchMode(\PDO::FETCH_CLASS, '\Core\Vo\User');
            $pst->execute();

            $users = $pst->fetchAll();
            $pst->closeCursor();
            unset($pst);

            $isOpen = false;
            foreach ($users as $user) {
                if (!$pst instanceof \PDOStatement) {
                    $pst = $con->prepare('SELECT a.id_photo, a.photo, a.perfil FROM users_photos AS a WHERE a.id_user = ?');
                }

                $pst->bindParam(1, $user->id);
                $pst->setFetchMode(\PDO::FETCH_CLASS, '\Core\Vo\Photo');
                $isOpen = $pst->execute();

                $user->photos = $pst->fetchAll();
            }

            if ($isOpen) {
                $pst->closeCursor();
            }

            unset($pst);

            return $users;
        } catch (\Exception $err) {
            throw $err;
        } finally {
            unset($con);
        }*/
    }

    public function insert(Like $like)
    {
        $con;
        try {
            $con = Conexao::getConexao();
            $con->beginTransaction();
            $pst = $con->prepare(
            'INSERT INTO likes(id_user, id_like, liked) VALUES (?, ?, ?);');

            $pst->bindParam(1, $like->user->id);
            $pst->bindParam(2, $like->like->id);
            $pst->bindParam(3, $like->liked, \PDO::PARAM_BOOL);
            $pst->execute();
            $pst->closeCursor();
            unset($pst);

            $like->id = $con->lastInsertId('likes_id_seq');

            $con->commit();

            return Utils::mapper($like, new Like());
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
