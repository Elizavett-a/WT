<?php
class EntityManager {
    private $pdo;

    public function __construct(array $config) {
        $dsn = "mysql:host={$config['host']};dbname=probook_db;charset=utf8mb4";
        $this->pdo = new PDO($dsn, $config['user'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}