<?php
namespace Src\Config;

use PDO;
use PDOException;

class Db
{
    public static function connect()
    {

        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $host = $_ENV['DB_HOST'];
        $db = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];

        try {
            return new PDO("pgsql:host=$host;dbname=$db", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            ]);
        } catch (PDOException $e) {
            throw new \Exception("Erro ao conectar ao banco de dados: " . $e->getMessage());
        }
    }
}
