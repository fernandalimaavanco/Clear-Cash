<?php
require_once '../utils/JwtUtils.php';

class AuthController
{
    private $db;
    private $jwtUtils;

    public function __construct($db)
    {
        $this->db = $db;
        $this->jwtUtils = new JwtUtils();
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
                http_response_code(200);
                echo json_encode(["token" => $token]);
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Credenciais inválidas."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Login e senha são obrigatórios."]);
        }
    }
}
