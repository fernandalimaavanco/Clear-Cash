<?php

require_once '../../vendor/autoload.php';

require_once '../config/db.php';
require_once '../controllers/User.php';
require_once '../controllers/Category.php';
require_once '../controllers/Operation.php';
require_once '../controllers/Auth.php';
require_once '../Router.php';
require_once '../middlewares/Auth.php';

$router = new Router();
$userController = new UserController($pdo);
$categoryController = new CategoryController($pdo);
$operationController = new OperationController($pdo);
$authController = new AuthController($pdo);

header("Content-type: application/json; charset=UTF-8");

$authMiddleware = new AuthMiddleware();

$router->add('POST','/login', function() use ($authController) { 
    $authController->login();
});

$router->add('POST','/users', function() use ($userController) {
    $userController->create();
});

$router->add('GET','/users', function() use ($userController, $authMiddleware) {
    $authMiddleware->handle(); 
    $userController->list();
});

$router->add('GET','/users/{userId}', function($userId) use ($userController, $authMiddleware) {
    $authMiddleware->handle(); 
    $userController->getById($userId);
});

$router->add('DELETE','/users/{userId}', function($userId) use ($userController, $authMiddleware) {
    $authMiddleware->handle(); 
    $userController->delete($userId);
});

$router->add('PUT','/users/{userId}', function($userId) use ($userController, $authMiddleware) {
    $authMiddleware->handle(); 
    $userController->update($userId);
});

$router->add('GET','/categories', function() use ($categoryController, $authMiddleware) {
    $authMiddleware->handle(); 
    $categoryController->list();
});

$router->add('GET','/categories/{categoryId}', function($categoryId) use ($categoryController, $authMiddleware) {
    $authMiddleware->handle(); 
    $categoryController->getById($categoryId);
});

$router->add('DELETE','/categories/{categoryId}', function($categoryId) use ($categoryController, $authMiddleware) {
    $authMiddleware->handle(); 
    $categoryController->delete($categoryId);
});

$router->add('POST','/categories', function() use ($categoryController, $authMiddleware) {
    $authMiddleware->handle(); 
    $categoryController->create();
});

$router->add('PUT','/categories/{categoryId}', function($categoryId) use ($categoryController, $authMiddleware) {
    $authMiddleware->handle(); 
    $categoryController->update($categoryId);
});

$router->add('POST','/operations', function() use ($operationController, $authMiddleware) {
    $authMiddleware->handle(); 
    $operationController->create();
});

$router->add('GET','/operations', function() use ($operationController, $authMiddleware) {
    $authMiddleware->handle(); 
    $operationController->list();
});

$router->add('GET','/operations/{operationId}', function($operationId) use ($operationController, $authMiddleware) {
    $authMiddleware->handle(); 
    $operationController->getById($operationId);
});

$router->add('PUT','/operations/{operationId}', function($operationId) use ($operationController, $authMiddleware) {
    $authMiddleware->handle(); 
    $operationController->update($operationId);
});

$router->add('DELETE','/operations/{operationId}', function($operationId) use ($operationController, $authMiddleware) {
    $authMiddleware->handle(); 
    $operationController->delete($operationId);
});


$requestedPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathItens = explode("/", $requestedPath);
$requestedPath = "/" . $pathItens[1] . ($pathItens[2] ? "/" . $pathItens[2] : '');

$router->dispatch($requestedPath);
