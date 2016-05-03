<?php

namespace Core\Routes;

use Core\Vo\User;
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
            $user = $this->dao->post(Utils::mapper(json_decode($req->getBody()->getContents()), new User()));

            return $res->withStatus(201)->withHeader('Location', $req->getUri().'/'.$user->id);
        } catch (\Exception $e) {
            throw $e;
        }
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
