<?php
require_once '../config/db.php';

class Operation
{
    private $connection;

    public function __construct($db)
    {
        $this->connection = $db;
    }

    public function create($value, $date, $id_user, $id_category)
    {
        $sql = "INSERT INTO tb_operations (value, date, id_user, id_category) VALUES (?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $value);
        $stmt->bindParam(2, $date);
        $stmt->bindParam(3, $id_user);
        $stmt->bindParam(4, $id_category);
        return $stmt->execute();
    }

    public function list()
    {
        $sql = "SELECT (id_operation, value, date, description) FROM tb_operations
                INNER JOIN tb_categories on tb_categories.id_category = tb_operations.id_category";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT (id_operation, value, date, description) FROM tb_operations
                INNER JOIN tb_categories on tb_categories.id_category = tb_operations.id_category
                WHERE id_operation = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $value, $date, $id_category)
    {
        $sql = "UPDATE tb_operations SET description = ?, entrance = ?, id_category = ? WHERE id_operation = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $value);
        $stmt->bindParam(2, $date);
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