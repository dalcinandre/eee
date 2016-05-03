<?php

namespace Routes;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Req;
use Psr\Http\Message\ResponseInterface as Res;
use Dao\ChatsDAO;

class Chats
{
    protected $ci;
    protected $dao;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
        $this->dao = new ChatsDAO();
    }

    public function get(Req $req, Res $res, array $args)
    {
        try {
            return $res->withStatus(200)->withJson($this->dao->get($req->getAttribute('idUser')));
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
