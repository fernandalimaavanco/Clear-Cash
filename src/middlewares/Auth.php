<?php

class AuthMiddleware
{
    private $jwtUtils;

    public function __construct()
    {
        $this->jwtUtils = new JwtUtils();
    }

    public function handle()
    {
        $headers = getallheaders();
        
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            $token = str_replace('Bearer ', '', $authHeader);

            if ($this->jwtUtils->validateToken($token)) {
                return true; 
            }
        }

        http_response_code(401); 
        echo json_encode(["message" => "Token não fornecido ou inválido."]);
        exit();
    }
}
