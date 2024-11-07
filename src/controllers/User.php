<?php
require_once '../models/User.php';

class UserController
{
    private $user;

    public function __construct($db)
    {
        $this->user = new User($db);
    }

    public function list()
    {
        try {
            $users = $this->user->list();
            http_response_code(200);
            echo json_encode($users);
        } catch (\Throwable $th) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao listar os usuários."]);
        }
    }

    public function create()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!empty($data['name']) && !empty($data['login']) && !empty($data['password'])) {
            try {
                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
                $this->user->create($data['name'], $data['login'], $hashedPassword);
                http_response_code(201);
                echo json_encode(["message" => "Usuário criado com sucesso."]);
            } catch (\Throwable $th) {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao criar o usuário."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos."]);
        }
    }

    public function getById($id)
    {
        if (!empty($id)) {
            try {
                $user = $this->user->getById($id);
                if ($user) {
                    http_response_code(200);
                    echo json_encode($user);
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Usuário não encontrado."]);
                }
            } catch (\Throwable $th) {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao buscar o usuário."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID inválido."]);
        }
    }

    public function update($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!empty($id) && !empty($data['name']) && !empty($data['login'])) {
            try {
                $count = $this->user->update($id, $data['name'], $data['login']);
                if ($count > 0) {
                    http_response_code(200);
                    echo json_encode(["message" => "Usuário atualizado com sucesso."]);
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Usuário não encontrado para atualização."]);
                }
            } catch (\Throwable $th) {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao atualizar o usuário."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos."]);
        }
    }

    public function delete($id)
    {
        if (!empty($id)) {
            try {
                $count = $this->user->delete($id);
                if ($count > 0) {
                    http_response_code(200);
                    echo json_encode(["message" => "Usuário deletado com sucesso."]);
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Usuário não encontrado para exclusão."]);
                }
            } catch (\Throwable $th) {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao deletar o usuário."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID inválido."]);
        }
    }
}
