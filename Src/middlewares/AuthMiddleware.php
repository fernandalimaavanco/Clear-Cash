<?php

namespace Src\Middlewares;
use Src\Utils\JwtUtils;

class AuthMiddleware
{
    private $jwtUtils;

    public function __construct()
    {
        $this->jwtUtils = new JwtUtils();
    }

    public function handle()
    {
        if (isset($_COOKIE['auth_token'])) {
            $token = $_COOKIE['auth_token'];

            if ($this->jwtUtils->validateToken($token)) {
                return true;
            }
        }

        http_response_code(401);
        echo json_encode(["message" => "Token não fornecido ou inválido."]);
        exit();
    }
}

