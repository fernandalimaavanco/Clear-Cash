<?php
require_once '../models/Operation.php';
require_once '../controllers/Category.php';
require_once '../utils/JwtUtils.php';

class OperationController
{
    private $operation;
    private $jwtUtils;
    private $category;

    public function __construct($db)
    {
        $this->operation = new Operation($db);
        $this->jwtUtils = new JwtUtils();
        $this->category = new CategoryController($db);
    }

    private function getUserIdFromToken()
    {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(["message" => "Token não fornecido."]);
            exit;
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $userData = $this->jwtUtils->decodeToken($token);

        if (!$userData || !isset($userData->id)) {
            http_response_code(401);
            echo json_encode(["message" => "Token inválido."]);
            exit;
        }

        return $userData->id;
    }

    public function list()
    {
        try {
            $operations = $this->operation->list();
            http_response_code(200);
            echo json_encode($operations);
        } catch (\Throwable $th) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao listar as operações."]);
        }
    }

    public function create()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['value']) || empty($data['date']) || empty($data['id_category'])) {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos."]);
            return;
        }

        $value = filter_var($data['value'], FILTER_VALIDATE_FLOAT);
        if ($value === false) {
            http_response_code(400);
            echo json_encode(["message" => "O campo 'value' deve ser um número válido."]);
            return;
        }

        $date = $data['date'];
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
            http_response_code(400);
            echo json_encode(["message" => "O campo 'date' deve estar no formato YYYY-MM-DD."]);
            return;
        }

        $id_category = filter_var($data['id_category'], FILTER_VALIDATE_INT);
        if ($id_category === false) {
            http_response_code(400);
            echo json_encode(["message" => "O campo 'id_category' deve ser um número inteiro válido."]);
            return;
        }

        if (!$this->category->exists($id_category)) {
            http_response_code(400);
            echo json_encode(["message" => "Categoria inválida: o ID fornecido não corresponde a uma categoria existente."]);
            return;
        }
        
        $id_user = $this->getUserIdFromToken();

        try {
            $this->operation->create($value, $date, $id_category, $id_user);
            http_response_code(201);
            echo json_encode(["message" => "Operação criada com sucesso."]);
        } catch (\Throwable $th) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao criar a operação."]);
        }
    }

    public function getById($id)
    {
        if (!empty($id)) {
            try {
                $operation = $this->operation->getById($id);
                if ($operation) {
                    http_response_code(200);
                    echo json_encode($operation);
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Operação não encontrada."]);
                }
            } catch (\Throwable $th) {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao buscar a operação."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID inválido."]);
        }
    }

    public function update($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($id) || empty($data['value']) || empty($data['date']) || empty($data['id_category'])) {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos."]);
            return;
        }

        $value = filter_var($data['value'], FILTER_VALIDATE_FLOAT);
        $date = $data['date'];
        $id_category = filter_var($data['id_category'], FILTER_VALIDATE_INT);
        
        if ($value === false || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $date) || $id_category === false) {
            http_response_code(400);
            echo json_encode(["message" => "Dados inválidos."]);
            return;
        }

        $id_user = $this->getUserIdFromToken();

        try {
            $count = $this->operation->update($id, $value, $date, $id_category, $id_user);
            if ($count > 0) {
                http_response_code(200);
                echo json_encode(["message" => "Operação atualizada com sucesso."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Operação não encontrada para atualização."]);
            }
        } catch (\Throwable $th) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao atualizar a operação."]);
        }
    }

    public function delete($id)
    {
        if (!empty($id)) {
            try {
                $count = $this->operation->delete($id);
                if ($count > 0) {
                    http_response_code(200);
                    echo json_encode(["message" => "Operação deletada com sucesso."]);
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Operação não encontrada para exclusão."]);
                }
            } catch (\Throwable $th) {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao deletar a operação."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID inválido."]);
        }
    }
}
