<?php

namespace Src\Models;
use PDO;
use Src\Config\Db;

class User
{
    private $connection;

    public function __construct()
    {
        $this->connection = Db::connect();
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
        $sql = "SELECT id_user, name FROM tb_users";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT id_user, name, login FROM tb_users WHERE id_user = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $name, $login)
    {
        $sql = "UPDATE tb_users SET name = ?, login = ? WHERE id_user = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $login);
        $stmt->bindParam(3, $id);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM tb_users WHERE id_user = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->rowCount();
    }
}