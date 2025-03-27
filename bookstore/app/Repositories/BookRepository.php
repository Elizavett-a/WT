<?php
class BookRepository {
    private $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function findAll(): array {
        return $this->em->query("SELECT * FROM books")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array {
        $stmt = $this->em->query("SELECT * FROM books WHERE id = ?", [$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}