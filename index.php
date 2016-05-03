<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header('Content-Type: application/json;charset=utf-8');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');

require realpath(__DIR__).'/vendor/autoload.php';

$app = new \Slim\App();
$c = $app->getContainer();
$c['errorHandler'] = function ($c) {
  return new Core\Utils\Error();
};

$app->group('/users', function () use ($app) {
  $app->get('[/]', 'Core\Routes\Users:get');
  $app->put('[/]', 'Core\Routes\Users:put');
  $app->post('[/]', 'Core\Routes\Users:post');
  $app->delete('/{id}', 'Core\Routes\Users:delete');
});

$app->group('/chats', function () use ($app) {
  $app->get('/{idUser}', 'Core\Routes\Chats:get');
});

$app->get('/ex', function () {
  throw new \Exception('teste de exception', 500);
});

$app->run();
