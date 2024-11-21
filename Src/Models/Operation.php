<?php

namespace Src\Models;
use PDO;
use Src\Config\Db;

class Operation
{
    private $connection;

    public function __construct()
    {
        $this->connection = Db::connect();
    }

    public function create($description, $value, $id_category, $id_user)
    {
        $sql = "INSERT INTO tb_operations (description, value, date, id_user, id_category) VALUES (?, ?, CURRENT_DATE, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $description);
        $stmt->bindParam(2, $value);
        $stmt->bindParam(3, $id_user);
        $stmt->bindParam(4, $id_category);
        return $stmt->execute();
    }

    public function list($idUser, $description = null)
    {
        $sql = "
        SELECT 
            id_operation AS id,
            tb_categories.id_category AS categoryid,
            value,
            date,
            tb_categories.description AS category,
            tb_operations.description,
            CASE 
                WHEN tb_categories.entrance = true THEN 'income'
                ELSE 'outcome'
            END AS type
        FROM 
            tb_operations
        INNER JOIN 
        tb_categories ON tb_categories.id_category = tb_operations.id_category
        WHERE id_user = ?";

        if ($description) {
            $sql .= " AND tb_operations.description ILIKE ?";
        }

        $stmt = $this->connection->prepare($sql);

        $stmt->bindParam(1, $idUser);

        if ($description) {
            $descriptionParam = '%' . $description . '%';
            $stmt->bindParam(2, $descriptionParam);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT id_operation as id,  id_category as categoryid, value, date, tb_operations.description, tb_categories.description, as category FROM tb_operations
                INNER JOIN tb_categories on tb_categories.id_category = tb_operations.id_category
                WHERE id_operation = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $description, $value, $id_category)
    {
        $sql = "UPDATE tb_operations SET description = ?, value = ?, id_category = ? WHERE id_operation = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $description);
        $stmt->bindParam(2, $value);
        $stmt->bindParam(3, $id_category);
        $stmt->bindParam(4, $id);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM tb_operations WHERE id_operation = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->rowCount();
    }
}