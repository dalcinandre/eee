<?php

namespace Core\Dao;

use Core\Vo\User;
use Core\Vo\Photo;
use Core\Vo\Gender;
use Core\Vo\Location;
use Core\Utils\Utils;

class UsersDAO
{
    public function __construct()
    {
    }

    public function retrieve($id, $latitude, $longitude)
    {
        $con;
        try {
            $con = Conexao::getConexao();
            $pst = $con->prepare(
              'SELECT
              	a.id_user AS id,
              	a.name,
              	a.last_name AS lastName,
              	a.birthday,
                a.bio
              FROM
              	get_users(?, ?, ?) AS a
              LIMIT
                20;'
            );

            $pst->bindParam(1, $id);
            $pst->bindParam(2, $latitude);
            $pst->bindParam(3, $longitude);

            $pst->setFetchMode(\PDO::FETCH_CLASS, '\Core\Vo\User');
            $pst->execute();

            $users = $pst->fetchAll();
            $pst->closeCursor();
            $pst = null;

            $isOpen = false;
            foreach ($users as $user) {
                if (!($pst instanceof \PDOStatement)) {
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
        }
    }

    public function insert(User $user)
    {
        $con;
        try {
            $con = Conexao::getConexao();
            $con->beginTransaction();
            $pst = $con->prepare(
            'INSERT INTO users(
                        name, username, password, birthday, interest_from, interest_to,
                        bio, congregation, id_gender, profession, latitude, longitude, radius, last_name)
                VALUES (?, ?, ?, ?, ?, ?, ?,
                        ?, ?, ?, ?, ?, ?, ?);');

            $pst->bindParam(1, $user->name);
            $pst->bindParam(2, $user->username);
            $pst->bindParam(3, $user->password);
            $pst->bindParam(4, $user->birthday);
            $pst->bindParam(5, $user->interestFrom);
            $pst->bindParam(6, $user->interestTo);
            $pst->bindParam(7, $user->bio);
            $pst->bindParam(8, $user->congregation);
            $pst->bindParam(9, $user->gender->id);
            $pst->bindParam(10, $user->profession);
            $pst->bindParam(11, $user->location->latitude);
            $pst->bindParam(12, $user->location->longitude);
            $pst->bindParam(13, $user->radius);
            $pst->bindParam(14, $user->lastName);
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

                    $photo->id = $con->lastInsertId('users_photos_id_photo_seq');
                }

                $pst->closeCursor();
                unset($pst);
            }

            $con->commit();

            return Utils::mapper($user, new User());
        } catch (\Exception $err) {
            if (($con instanceof \PDO) && ($con->inTransaction())) {
                $con->rollBack();
            }

            throw $err;
        } finally {
            unset($con);
        }
    }

    public function update(User $user)
    {
        $con;
        try {
            $con = Conexao::getConexao();
            $con->beginTransaction();
            $pst = $con->prepare(
              'UPDATE users SET
                name=?,
                birthday=?,
                interest_from=?,
                interest_to=?,
                bio=?,
                congregation=?,
                id_gender=?,
                profession=?,
                latitude=?,
                longitude=?,
                radius=?,
                last_name=?
              WHERE
                id_user = ?;'
            );

            $pst->bindParam(1, $user->name);
            $pst->bindParam(2, $user->birthday);
            $pst->bindParam(3, $user->interestFrom);
            $pst->bindParam(4, $user->interestTo);
            $pst->bindParam(5, $user->bio);
            $pst->bindParam(6, $user->congregation);
            $pst->bindParam(7, $user->gender->id);
            $pst->bindParam(8, $user->profession);
            $pst->bindParam(9, $user->location->latitude);
            $pst->bindParam(10, $user->location->longitude);
            $pst->bindParam(11, $user->radius);
            $pst->bindParam(12, $user->lastName);
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
                    case 1:
                    {
                      $pstI->bindParam(1, $user->id);
                      $pstI->bindParam(2, $photo->photo);
                      $pstI->bindParam(3, $photo->perfil, \PDO::PARAM_BOOL);
                      $pstI->execute();

                      $photo->id = $con->lastInsertId('users_photos_id_photo_seq');
                    } break;

                    case 2:
                    {
                      $pstU->bindParam(1, $photo->photo);
                      $pstU->bindParam(2, $photo->perfil, \PDO::PARAM_BOOL);
                      $pstU->bindParam(3, $photo->id);
                      $pstU->execute();
                    } break;

                    case 3:
                    {
                      $pstD->bindParam(1, $photo->id);
                      $pstD->execute();
                    } break;

                    default:
                      break;
                  }

                    $photo->status = null;
                }

                $pstI->closeCursor();
                unset($pstI);

                $pstU->closeCursor();
                unset($pstU);

                $pstD->closeCursor();
                unset($pstD);
            }

            $con->commit();

            return Utils::mapper($user, new User());
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

    public function login(User $user)
    {
        $con;
        try {
            $con = Conexao::getConexao();
            $pst = $con->prepare(
              'SELECT
                a.id_user as id,
                a.name,
              	a.username as userName,
              	a.birthday,
              	a.interest_from interestFrom,
              	a.interest_to interestTo,
              	a.bio,
              	a.congregation,
              	a.latitude,
              	a.longitude,
              	a.radius,
                a.id_gender as gender,
              	a.last_name as lastName
              FROM
              	users AS a
              WHERE
              	a.username = ? AND
              	a.password = ?'
            );

            $pst->bindParam(1, $user->username);
            $pst->bindParam(2, $user->password);
            $pst->setFetchMode(\PDO::FETCH_CLASS, '\Core\Vo\User');
            $pst->execute();

            $user = $pst->fetch();
            $pst->closeCursor();
            unset($pst);

            if (empty($user)) {
                return;
            }

            $location = new Location();
            $location->latitude = $user->latitude;
            $location->longitude = $user->longitude;
            $user->location = $location;

            $gender = new Gender();
            $gender->id = $user->gender;
            $user->gender = $gender;

            $user->latitude = $user->longitude = null;

            $pst = $con->prepare('SELECT a.id_photo AS id, a.photo, a.perfil FROM users_photos AS a WHERE a.id_user = ?;');
            $pst->bindParam(1, $user->id);
            $pst->setFetchMode(\PDO::FETCH_CLASS, 'Core\Vo\Photo');
            $pst->execute();
            $user->photos = $pst->fetchAll();
            $pst->closeCursor();
            unset($pst);

            return Utils::mapper($user, new User());
        } catch (\Exception $err) {
            throw $err;
        } finally {
            unset($con);
        }
    }
}
