<?php
namespace App\Repositories;

use App\Database\EntityManager;
use App\Models\Book;
use PDO;

class BookRepository {
    private EntityManager $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function findAll(): array {
        $stmt = $this->em->query("SELECT * FROM books");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($data)) {
            throw new \RuntimeException("No books found in database");
        }

        return array_map(function($item) {
            $book = new Book($item);
            error_log(print_r($book->toArray(), true));
            return $book;
        }, $data);
    }

    public function findById($id): ?Book {
        $stmt = $this->em->query("SELECT * FROM books WHERE id = :id", ['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new Book($data) : null;
    }
}