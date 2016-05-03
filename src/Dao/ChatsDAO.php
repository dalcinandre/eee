<?php

namespace Core\Dao;

use Core\Vo\Chat;
use Core\Vo\User;
use Core\Utils\Utils;

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
}
