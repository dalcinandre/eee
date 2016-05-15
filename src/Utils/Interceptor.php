<?php

namespace Core\Utils;

class Interceptor
{
    public function __construct()
    {
    }

    public function __invoke($request, $response, $next)
    {
        $response->getBody()->write('BEFORE');
        $response = $next($request, $response);
        $response->getBody()->write('AFTER');

        return $response;
    }
}
