<?php
declare(strict_types = 1);

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
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    public function beginTransaction(): void {
        $this->pdo->beginTransaction();
    }

    public function commit(): void {
        $this->pdo->commit();
    }



    public function lastInsertId(): string {
        return $this->pdo->lastInsertId();
    }

    public function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function prepare(string $sql): bool|PDOStatement
    {
        return $this->pdo->prepare($sql);
    }
}