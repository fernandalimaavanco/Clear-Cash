<?php
require_once '../config/db.php';
require_once '../controllers/User.php';
require_once '../Router.php';

$router = new Router();
$userController = new UserController($pdo);

header("Content-type: application/json; charset=UTF-8");

$router->add('GET','/users', [$userController, 'list']);
$router->add('GET','/user/{userId}', [$userController, 'getById']);
$router->add('DELETE','/user/{userId}', [$userController, 'delete']);
$router->add('POST','/user', [$userController, 'getById']);
$router->add('PUT','/user', [$userController, 'getById']);

$requestedPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathItens = explode("/", $requestedPath);
$requestedPath = "/" . $pathItens[2] . ($pathItens[3] ? "/" . $pathItens[3] : '');

$router->dispatch($requestedPath);
