<?php

namespace Core\Routes;

use Core\Dao\LocationDAO;
use Core\Utils\Utils;
use Core\Vo\User;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Req;
use Psr\Http\Message\ResponseInterface as Res;

class Location
{
    protected $ci;
    protected $dao;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
        $this->dao = new LocationDAO();
    }

    public function put(Req $req, Res $res, array $args)
    {
        try {
            $this->dao->put(Utils::mapper(json_decode($req->getBody()->getContents()), new User()));

            return $res->withStatus(200)->withJson([]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
