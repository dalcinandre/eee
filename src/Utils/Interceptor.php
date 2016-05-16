<?php

namespace Core\Utils;

use Core\Dao\UsersDAO as Dao;
use Psr\Http\Message\ServerRequestInterface as Req;
use Psr\Http\Message\ResponseInterface as Res;

class Interceptor
{
    private $con;

    public function __construct()
    {
        $this->con = new Dao();
    }

    public function __invoke(Req $req, Res $res, callable $next)
    {
        if ($req->hasHeader('Token') /*&& $dao->isValidToken('')*/) {
            $res = $next($req, $res);
        } else {
            $res = $res->withStatus(401);
        }

        return $res;
    }
}
