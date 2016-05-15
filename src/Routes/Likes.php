<?php

namespace Core\Routes;

use Core\Dao\LikesDAO;
use Core\Vo\Like;
use Core\Utils\Utils;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Req;
use Psr\Http\Message\ResponseInterface as Res;

class Likes
{
    protected $ci;
    protected $dao;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
        $this->dao = new LikesDAO();
    }

    public function retrieve(Req $req, Res $res, array $args)
    {
        try {
            return $res->withStatus(200)->withJson(
              $this->dao->retrieve(
                $req->getAttribute('id'),
                $req->getAttribute('limit'),
                $req->getAttribute('offset')
              )
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function insert(Req $req, Res $res, array $args)
    {
        try {
            $like = $this->dao->insert(Utils::mapper(json_decode($req->getBody()->getContents()), new Like()));

            return $res->withStatus(201)->withJson($like)->withHeader('Location', $req->getUri().'/'.$like->id);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
