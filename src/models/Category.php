<?php
require_once '../config/db.php';

class Category
{
    private $connection;

    public function __construct($db)
    {
        $this->connection = $db;
    }

    public function create($description, $entrance)
    {
        $sql = "INSERT INTO tb_categories (description, entrance) VALUES (?, ?)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $description);
        $stmt->bindParam(2, $entrance);
        return $stmt->execute();
    }

    public function list()
    {
        $sql = "SELECT * FROM tb_categories";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM tb_categories WHERE id_category = ?";
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