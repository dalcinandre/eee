<?php

namespace Routes;

use Vo\User;
use Dao\UsersDAO;
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

    public function get(Req $req, Res $res, array $args)
    {
    }

    public function put(Req $req, Res $res, array $args)
    {
        try {
            $user = new User();

            foreach (get_object_vars((object) $req->getParsedBody()) as $key => $value) {
                $user->{$key} = $value;
            }

            $this->dao->put($user);

            return $res->withStatus(200)->withJson($user);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function post(Req $req, Res $res, array $args)
    {
        try {
            $user = new User();

            $user = $this->teste($req->getParsedBody(), $user);

            throw new \Exception($user);

            /*
            foreach (get_object_vars((object) $req->getParsedBody()) as $key => $value) {
                $user->{$key} = $value;
            }
            */

            $user = $this->dao->post($user);

            return $res->withStatus(201)->withHeader('Location', $req->getUri().'/'.$user->id);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function teste($t, $user)
    {
        foreach (get_object_vars($t) as $key => $value) {
            if (get_object_vars($value) > 0) {
                $this->teste($value, $user->{$key});
            }

            $user->{$key} = $value;
        }

        return $user;
    }

    public function delete(Req $req, Res $res, array $args)
    {
        try {
            $this->dao->delete($req->getAttribute('id'));

            return $res->withStatus(200);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
