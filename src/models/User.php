<?php
require_once '../config/db.php';

class User
{
    private $connection;

    public function __construct($db)
    {
        $this->connection = $db;
    }

    public function create($name, $login, $password)
    {
        $sql = "INSERT INTO tb_users (name, login, password) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $login);
        $stmt->bindParam(3, $password);
        return $stmt->execute();
    }

    public function list()
    {
        $sql = "SELECT id, name FROM tb_users";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT id, name, login FROM tb_users WHERE id = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $name, $login)
    {
        $sql = "UPDATE tb_users SET name = ?, login = ? WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $login);
        $stmt->bindParam(3, $id);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM tb_users WHERE id = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->rowCount();
    }
}