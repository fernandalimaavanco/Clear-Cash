<?php

namespace Src\Controllers;
use Src\Models\Operation;
use Src\Controllers\CategoryController;
use Src\Utils\JwtUtils;

class OperationController
{
    private $operation;
    private $jwtUtils;
    private $categoryController;

    public function __construct(Operation $operation = null, JwtUtils $jwtUtils = null, CategoryController $categoryController = null)
    {
        $this->operation = $operation ?? new Operation();
        $this->jwtUtils = $jwtUtils ?? new JwtUtils();
        $this->categoryController = $categoryController ?? new CategoryController();
    }

    private function getUserIdFromToken()
    {
        if (!isset($_COOKIE['auth_token'])) {
            http_response_code(401);
            echo json_encode(["message" => "Token não fornecido."]);
            exit;
        }

        $token = str_replace('auth_token ', '', $_COOKIE['auth_token']);
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

            $description = $_GET['description'] ?? null;

            $idUser = $this->getUserIdFromToken();

            $operations = $this->operation->list($idUser, $description);
            http_response_code(200);
            echo json_encode($operations, JSON_NUMERIC_CHECK);
        } catch (\Throwable $th) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao listar as operações."]);
        }
    }

    public function create($data)
    {

        if (empty($data['value']) || empty($data['id_category']) || empty($data['description'])) {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos."]);
            return;
        }

        $description = htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8');

        $value = filter_var($data['value'], FILTER_VALIDATE_FLOAT);
        if ($value === false) {
            http_response_code(400);
            echo json_encode(["message" => "O campo 'value' deve ser um número válido."]);
            return;
        }

        $id_category = filter_var($data['id_category'], FILTER_VALIDATE_INT);
        if ($id_category === false) {
            http_response_code(400);
            echo json_encode(["message" => "O campo 'id_category' deve ser um número inteiro válido."]);
            return;
        }

        if (!$this->categoryController->exists($id_category)) {
            http_response_code(400);
            echo json_encode(["message" => "Categoria inválida: o ID fornecido não corresponde a uma categoria existente."]);
            return;
        }

        $id_user = $this->getUserIdFromToken();

        try {
            $this->operation->create($description, $value, $id_category, $id_user);
            http_response_code(201);
            echo json_encode(["message" => "Operação criada com sucesso."]);
        } catch (\Throwable $th) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao criar a operação." . $th->getMessage()]);
        }
    }

    public function getById($id)
    {
        if (!empty($id) && filter_var($id, FILTER_VALIDATE_INT) !== false) {
            try {
                $idUser = $this->getUserIdFromToken();
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

    public function update($data, $id)
    {
        if (empty($id) || empty($data['description']) || empty($data['value']) || empty($data['id_category'])) {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos."]);
            return;
        }

        $value = filter_var($data['value'], FILTER_VALIDATE_FLOAT);
        $description = htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8');
        $id_category = filter_var($data['id_category'], FILTER_VALIDATE_INT);

        if ($value === false || $id_category === false) {
            http_response_code(400);
            echo json_encode(["message" => "Dados inválidos."]);
            return;
        }

        $operation = $this->operation->getById($id);
        if (!$operation) {
            http_response_code(404);
            echo json_encode(["message" => "Operação não encontrada para atualização."]);
            return;
        }

        if (!$this->categoryController->exists($id_category)) {
            http_response_code(400);
            echo json_encode(["message" => "Categoria inválida: o ID fornecido não corresponde a uma categoria existente."]);
            return;
        }

        $id_user = $this->getUserIdFromToken();

        if ($operation['id_user'] !== $id_user) {
            http_response_code(403);
            echo json_encode(["message" => "Você não tem permissão para atualizar esta operação."]);
            return;
        }

        try {
            $this->operation->update($id, $description, $value, $id_category);
            http_response_code(200);
            echo json_encode(["message" => "Operação atualizada com sucesso."]);
        } catch (\Throwable $th) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao atualizar a operação."]);
        }
    }

    public function delete($id)
    {
        if (!empty($id) && filter_var($id, FILTER_VALIDATE_INT) !== false) {
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
