<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Token, Authorization');
header('Content-Type: application/json;charset=utf-8');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');

require realpath(__DIR__).'/vendor/autoload.php';

use Core\Utils\Interceptor;

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$c = new \Slim\Container($configuration);

$c['errorHandler'] = function ($c) {
  return new Core\Utils\Error();
};
$app = new \Slim\App($c);
$app->add(new Interceptor());

$app->group('/users', function () use ($app) {
  $app->get('/{id}/{latitude}/{longitude}', 'Core\Routes\Users:retrieve');
  $app->put('[/]', 'Core\Routes\Users:update');
  $app->post('[/]', 'Core\Routes\Users:insert');
  $app->delete('/{id}', 'Core\Routes\Users:delete');

  $app->group('/likes', function () use ($app) {
    $app->post('[/]', 'Core\Routes\Likes:insert');
    # $app->get('/{id}[/{limit:\d+?}/{offset:\d+?}]', 'Core\Routes\Likes:get');
  });

  $app->group('/location', function () use ($app) {
    $app->put('[/]', 'Core\Routes\Location:update');
  });
});

$app->post('/login[/]', 'Core\Routes\Users:login');

$app->group('/chats', function () use ($app) {
  $app->get('/{idUser}', 'Core\Routes\Chats:retrieve');
  $app->delete('/{id}/{idDislike}', 'Core\Routes\Chats:delete');
});

$app->run();
