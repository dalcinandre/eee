<?php

namespace Core\Dao;

use Core\Vo\User;
use Core\Utils\Utils;

class LocationDAO
{
    public function __construct()
    {
    }

    public function update(User $user)
    {
        $con;
        try {
            $con = Conexao::getConexao();
            $con->beginTransaction();
            $pst = $con->prepare(
              'UPDATE users SET
                latitude=?,
                longitude=?,
                push_id=?,
                city=?,
                state=?
              WHERE
                id_user = ?;'
            );

            $cities = Utils::getCities($user->location->latitude, $user->location->longitude);

            $pst->bindParam(1, $user->location->latitude);
            $pst->bindParam(2, $user->location->longitude);
            $pst->bindParam(3, $user->pushId);
            $pst->bindParam(4, $cities['city']);
            $pst->bindParam(5, $cities['state']);
            $pst->bindParam(6, $user->id, \PDO::PARAM_INT);
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
