<?php

namespace Src\Models;
use PDO;
use Src\Config\Db;

class Category
{
    private $connection;

    public function __construct()
    {
        $this->connection = Db::connect();
    }

    public function create($description, $entrance)
    {
        $sql = "INSERT INTO tb_categories (description, entrance) VALUES (?, ?)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $description);
        $stmt->bindParam(2, $entrance);
        return $stmt->execute();
    }

    public function list($description = null)
    {
        $sql = "SELECT id_category as id, description, entrance FROM tb_categories";

        if ($description) {
            $sql .= " WHERE description ILIKE ?";
        }

        $stmt = $this->connection->prepare($sql);

        if ($description) {
            $descriptionParam = '%' . $description . '%';
            $stmt->bindParam(1, $descriptionParam);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT id_category as id, description, entrance FROM tb_categories WHERE id_category = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $description, $entrance)
    {
        $sql = "UPDATE tb_categories SET description = ?, entrance = ? WHERE id_category = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $description);
        $stmt->bindParam(2, $entrance);
        $stmt->bindParam(3, $id);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM tb_categories WHERE id_category = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->rowCount();
    }
}
