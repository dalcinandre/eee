<?php

namespace Utils;

use Psr\Http\Message\ServerRequestInterface as Req;
use Psr\Http\Message\ResponseInterface as Res;

final class Error extends \Slim\Handlers\Error
{
    public function __construct()
    {
    }

    public function __invoke(Req $req, Res $res, \Exception $ex)
    {
        return $res
                ->withStatus(500)
                ->withHeader('Content-type', 'application/json')
                ->withJson(
                  [
                    'message' => $ex->getMessage(),
                    'code' => $ex->getCode(),
                    'file' => $ex->getFile(),
                    'line' => $ex->getLine(),
                    'trace' => $ex->getTraceAsString(),
                  ]);
    }
}
