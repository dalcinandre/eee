<?php

namespace Core\Routes;

use Core\Vo\User;
use Core\Vo\Location;
use Core\Dao\UsersDAO;
use Core\Utils\Utils;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Req;
use Psr\Http\Message\ResponseInterface as Res;

class Users
{
    protected $ci;
    protected $dao;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
        $this->dao = new UsersDAO();
    }

    public function retrieve(Req $req, Res $res, array $args)
    {
        try {
            return $res->withStatus(200)->withJson(
              $this->dao->retrieve(
                $req->getAttribute('id'), $req->getAttribute('latitude'), $req->getAttribute('longitude'))
              );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function update(Req $req, Res $res, array $args)
    {
        try {
            $user = $this->dao->update(Utils::mapper(json_decode($req->getBody()->getContents()), new User()));

            return $res->withStatus(200)->withJson($user);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function insert(Req $req, Res $res, array $args)
    {
        try {
            $user = $this->dao->insert(Utils::mapper(json_decode($req->getBody()->getContents()), new User()));

            return $res->withStatus(201)->withJson($user)->withHeader('Location', $req->getUri().'/'.$user->id);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function login(Req $req, Res $res, array $args)
    {
        try {
            $user = $this->dao->login(Utils::mapper(json_decode($req->getBody()->getContents()), new User()));

            if ($user instanceof User) {
                return $res->withStatus(200)->withJson($user);
            } else {
                return $res->withStatus(401);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function delete(Req $req, Res $res, array $args)
    {
        try {
            $this->dao->delete($req->getAttribute('id'));

            return $res->withStatus(200)->withJson([]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
