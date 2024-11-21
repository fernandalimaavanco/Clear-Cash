<?php
namespace Src\Controllers;

use Src\Models\Category;

class CategoryController
{
    private $category;

    public function __construct(Category $category = null)
    {
        $this->category = $category ?? new Category();
    }

    public function list()
    {
        try {

            $description = $_GET['description'] ?? null;

            $categories = $this->category->list($description);

            if (empty($categories)) {
                http_response_code(404);
                echo json_encode(["message" => "Nenhuma categoria encontrada."]);
                return;
            }

            http_response_code(200);
            echo json_encode(value: $categories);
        } catch (\Throwable $th) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao listar as categorias."]);
        }
    }

    public function create($data)
    {
        if (!empty($data['description']) && isset($data['entrance'])) {
            try {
                $this->category->create($data['description'], $data['entrance']);
                http_response_code(201);
                echo json_encode(["message" => "Categoria criada com sucesso."]);
            } catch (\Throwable $th) {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao criar a categoria."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos."]);
        }
    }

    public function getById($id)
    {
        if (!empty($id) && filter_var($id, FILTER_VALIDATE_INT) !== false) {
            try {
                $category = $this->category->getById($id);
                if ($category) {
                    http_response_code(200);
                    echo json_encode($category);
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Categoria não encontrada."]);
                }
            } catch (\Throwable $th) {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao buscar a categoria."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID inválido."]);
        }
    }

    public function update($data, $id)
    {
        if (!empty($id) && !empty($data['description']) && isset($data['entrance'])) {
            if (filter_var($id, FILTER_VALIDATE_INT) !== false) {
                try {
                    $count = $this->category->update($id, $data['description'], $data['entrance']);
                    if ($count > 0) {
                        http_response_code(200);
                        echo json_encode(["message" => "Categoria atualizada com sucesso."]);
                    } else {
                        http_response_code(404);
                        echo json_encode(["message" => "Categoria não encontrada para atualização."]);
                    }
                } catch (\Throwable $th) {
                    http_response_code(500);
                    echo json_encode(["message" => "Erro ao atualizar a categoria."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["message" => "ID inválido."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Dados incompletos."]);
        }
    }

    public function delete($id)
    {
        if (!empty($id) && filter_var($id, FILTER_VALIDATE_INT) !== false) {
            try {
                $count = $this->category->delete($id);
                if ($count > 0) {
                    http_response_code(200);
                    echo json_encode(["message" => "Categoria deletada com sucesso."]);
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Categoria não encontrada para exclusão."]);
                }
            } catch (\Throwable $th) {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao deletar a categoria."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID inválido."]);
        }
    }

    public function exists($idCategory)
    {
        if (!empty($idCategory)) {
            try {
                $category = $this->category->getById($idCategory);

                return $category ? true : false;
            } catch (\Throwable $th) {
                echo json_encode(["message" => "Erro ao buscar a categoria."]);
            }
        } else {
            echo json_encode(["message" => "ID inválido."]);
        }
    }
}
