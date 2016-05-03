<?php

namespace Core\Dao;

use Core\Vo\User;

class UsersDAO
{
    public function __construct()
    {
    }

    public function get()
    {
    }

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
            $pst->bindParam(9, $user->genre->id);
            $pst->bindParam(10, $user->profession);
            $pst->bindParam(11, $user->location);
            $pst->bindParam(12, $user->radius);
            $pst->execute();
            $pst->closeCursor();
            unset($pst);

            $user->id = $con->lastInsertId('users_id_user_seq');

            $photos = $user->photos;

            if (!empty($photos)) {
                $pst = $con->prepare('INSERT INTO users_photos(id_user, photo, perfil) VALUES (?, ?, ?);');

                foreach ($photos as $photo) {
                    $pst->bindParam(1, $user->id);
                    $pst->bindParam(2, $photo->photo);
                    $pst->bindParam(3, $photo->perfil, \PDO::PARAM_BOOL);
                    $pst->execute();
                }

                $pst->closeCursor();
                unset($pst);
            }

            $con->commit();

            return $user;
        } catch (\Exception $err) {
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
            $pst->bindParam(9, $user->genre->id);
            $pst->bindParam(10, $user->profession);
            $pst->bindParam(11, $user->location);
            $pst->bindParam(12, $user->radius);
            $pst->bindParam(13, $user->id, \PDO::PARAM_INT);
            $pst->execute();
            $pst->closeCursor();
            unset($pst);

            $photos = $user->photos;

            if (!empty($photos)) {
                $pstI = $con->prepare('INSERT INTO users_photos(id_user, photo, perfil) VALUES (?, ?, ?);');
                $pstU = $con->prepare('UPDATE users_photos SET photo=?, perfil=? WHERE id_photo=?;');
                $pstD = $con->prepare('DELETE FROM users_photos WHERE id_photo=?;');

                foreach ($photos as $photo) {
                    switch ($photo->status) {
                    case 0:
                    {
                      $pstI->bindParam(1, $user->id);
                      $pstI->bindParam(2, $photo->photo);
                      $pstI->bindParam(3, $photo->perfil, \PDO::PARAM_BOOL);
                      $pstI->execute();
                    } break;

                    case 1:
                    {
                      $pstU->bindParam(1, $photo->photo);
                      $pstU->bindParam(2, $photo->perfil, \PDO::PARAM_BOOL);
                      $pstU->bindParam(3, $photo->id);
                      $pstU->execute();
                    } break;

                    case 2:
                    {
                      $pstD->bindParam(1, $photo->id);
                      $pstD->execute();
                    } break;

                    default:
                      break;
                  }
                }

                $pstI->closeCursor();
                unset($pstI);

                $pstU->closeCursor();
                unset($pstU);

                $pstD->closeCursor();
                unset($pstD);
            }

            $con->commit();

            return $user;
        } catch (\Exception $err) {
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
