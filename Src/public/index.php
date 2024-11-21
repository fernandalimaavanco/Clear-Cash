<?php

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: http://localhost:5173");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    http_response_code(200);
    exit();
}

header("Content-type: application/json; charset=UTF-8");

require_once '../../vendor/autoload.php';

use Src\Controllers\CategoryController;
use Src\Controllers\AuthController;
use Src\Controllers\UserController;
use Src\Controllers\OperationController;
use Src\Middlewares\AuthMiddleware;

use Src\Router;

$router = new Router();
$userController = new UserController();
$categoryController = new CategoryController();
$operationController = new OperationController();
$authController = new AuthController();

$authMiddleware = new AuthMiddleware();

$router->add('POST', '/login', function () use ($authController) {
    $authController->login();
});

$router->add('POST', '/users', function () use ($userController) {
    $userController->create(json_decode(file_get_contents("php://input"), true));
});

$router->add('GET', '/validate-token', function () use ($authController) {
    $authController->verifyAcess();
});

$router->add('GET', '/users', function () use ($userController, $authMiddleware) {
    $authMiddleware->handle();
    $userController->list();
});

$router->add('GET', '/users/{userId}', function ($userId) use ($userController, $authMiddleware) {
    $authMiddleware->handle();
    $userController->getById($userId);
});

$router->add('DELETE', '/users/{userId}', function ($userId) use ($userController, $authMiddleware) {
    $authMiddleware->handle();
    $userController->delete($userId);
});

$router->add('PUT', '/users/{userId}', function ($userId) use ($userController, $authMiddleware) {
    $authMiddleware->handle();
    $userController->update(json_decode(file_get_contents("php://input"), true), $userId);
});

$router->add('GET', '/categories', function () use ($categoryController, $authMiddleware) {
    $authMiddleware->handle();
    $categoryController->list();
});

$router->add('GET', '/categories/{categoryId}', function ($categoryId) use ($categoryController, $authMiddleware) {
    $authMiddleware->handle();
    $categoryController->getById($categoryId);
});

$router->add('DELETE', '/categories/{categoryId}', function ($categoryId) use ($categoryController, $authMiddleware) {
    $authMiddleware->handle();
    $categoryController->delete($categoryId);
});

$router->add('POST', '/categories', function () use ($categoryController, $authMiddleware) {
    $authMiddleware->handle();
    $categoryController->create(json_decode(file_get_contents("php://input"), true));
});

$router->add('PUT', '/categories/{categoryId}', function ($categoryId) use ($categoryController, $authMiddleware) {
    $authMiddleware->handle();
    $categoryController->update($categoryId, json_decode(file_get_contents("php://input"), true));
});

$router->add('POST', '/operations', function () use ($operationController, $authMiddleware) {
    $authMiddleware->handle();
    $operationController->create(json_decode(file_get_contents("php://input"), true));
});

$router->add('GET', '/operations', function () use ($operationController, $authMiddleware) {
    $authMiddleware->handle();
    $operationController->list();
});

$router->add('GET', '/operations/{operationId}', function ($operationId) use ($operationController, $authMiddleware) {
    $authMiddleware->handle();
    $operationController->getById($operationId);
});

$router->add('PUT', '/operations/{operationId}', function ($operationId) use ($operationController, $authMiddleware) {
    $authMiddleware->handle();
    $operationController->update(json_decode(file_get_contents("php://input"), true), $operationId);
});

$router->add('DELETE', '/operations/{operationId}', function ($operationId) use ($operationController, $authMiddleware) {
    $authMiddleware->handle();
    $operationController->delete($operationId);
});


$requestedPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathItens = explode("/", $requestedPath);
if (isset($pathItens[3])) {
    $requestedPath = "/" . $pathItens[3];

    if (isset($pathItens[4])) {
        $requestedPath .= "/" . $pathItens[4];
    }
}

$router->dispatch($requestedPath);
