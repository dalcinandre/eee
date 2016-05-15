<?php

namespace Core\Dao;

use Core\Vo\User;

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
              longitude=?
            WHERE
              id_user = ?;'
          );

            $pst->bindParam(1, $user->location->latitude);
            $pst->bindParam(2, $user->location->longitude);
            $pst->bindParam(3, $user->id, \PDO::PARAM_INT);
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
