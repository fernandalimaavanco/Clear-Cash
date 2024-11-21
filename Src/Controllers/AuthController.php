<?php

namespace Src\Controllers;
use PDO;
use Src\Utils\JwtUtils;
use Src\Config\Db;

class AuthController
{
    private $db;
    private $jwtUtils;

    public function __construct()
    {
        $this->db = Db::connect();
        $this->jwtUtils = new JwtUtils();
    }

    public function verifyAcess()
    {
        $token = $_COOKIE['auth_token'] ?? null;

        if ($token && $this->jwtUtils->validateToken($token)) {
            http_response_code(200);
            echo json_encode(["message" => "Usuário não autenticado."]);
        } else {
            http_response_code(200);
            echo json_encode(["message" => "Usuário autenticado."]);
        }
    }

    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!empty($data['login']) && !empty($data['password'])) {

            $query = "SELECT * FROM tb_users WHERE login = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $data['login']);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($data['password'], $user['password'])) {

                $token = $this->jwtUtils->generateToken(['id' => $user['id_user']]);

                $cookie_name = 'auth_token';
                $cookie_value = $token;
                $cookie_expire = time() + 7200;
                $cookie_path = '/';
                $cookie_domain = 'localhost';
                $cookie_secure = false;
                $cookie_httponly = true;

                setcookie($cookie_name, $cookie_value, $cookie_expire, $cookie_path, $cookie_domain, $cookie_secure, $cookie_httponly);

                http_response_code(200);
                echo json_encode(["message" => "Login realizado com sucesso."]);

            } else {
                http_response_code(401);
                echo json_encode(["message" => "Credenciais inválidas."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Login e senha são obrigatórios."]);
        }
    }
    public function logout()
    {
        setcookie('auth_token', '', time() - 7200, '/', 'localhost', false, true);

        http_response_code(200);
        echo json_encode(["message" => "Logout realizado com sucesso."]);
    }
}
