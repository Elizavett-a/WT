<?php
namespace App\Database;

use PDO;
use PDOStatement;

class EntityManager {
    private PDO $pdo;

    public function __construct(array $config) {
        $this->pdo = new PDO(
            "mysql:host={$config['host']};dbname=probook_db",
            $config['user'],
            $config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    public function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}