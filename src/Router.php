<?php

class Router
{
    private $routes = [];

    public function add($method, $route, $callback)
    {
        $this->routes[] = [
            'method' => $method,
            'route' => $route,
            'callback' => $callback
        ];
    }
    public function dispatch($requestedPath)
    {
        $method = $_SERVER['REQUEST_METHOD']; 

        foreach ($this->routes as $route) {

            if ($method == $route['method'] && $this->matchRoute($route['route'], $requestedPath, $params)) {
                call_user_func_array($route['callback'], $params);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(["message" => "Rota não encontrada."]);
    }

    private function matchRoute($route, $requestedPath, &$params)
    {
        $route = trim($route, '/');
        $requestedPath = trim($requestedPath, '/');

        $routeParts = explode('/', $route);
        $requestedParts = explode('/', $requestedPath);

        if (count($routeParts) != count($requestedParts)) {
            return false;
        }

        $params = [];

        for ($i = 0; $i < count($routeParts); $i++) {
            if (isset($routeParts[$i]) && strpos($routeParts[$i], '{') !== false) {
                $paramName = trim($routeParts[$i], '{}');
                $params[$paramName] = $requestedParts[$i]; 
            } elseif ($routeParts[$i] !== $requestedParts[$i]) {
                return false;
            }
        }

        return true;
    }
}