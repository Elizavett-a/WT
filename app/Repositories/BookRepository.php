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
        $id = (int)$id;
        error_log("Выполняется запрос для ID: " . $id);
        $stmt = $this->em->query("SELECT * FROM books WHERE id = :id", ['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Результат запроса: " . print_r($data, true));
        return $data ? new Book($data) : null;
    }

    public function save(Book $book): void
    {
        if ($book->getId()) {
            // Обновление существующей книги
            $this->em->query(
                "UPDATE books SET title = :title, author = :author, price = :price, cover = :cover WHERE id = :id",
                [
                    'title' => $book->getTitle(),
                    'author' => $book->getAuthor(),
                    'price' => $book->getPrice(),
                    'cover' => $book->getCover(),
                    'id' => $book->getId()
                ]
            );
        } else {
            // Создание новой книги (на будущее)
            $this->em->query(
                "INSERT INTO books (title, author, price, cover) VALUES (:title, :author, :price, :cover)",
                [
                    'title' => $book->getTitle(),
                    'author' => $book->getAuthor(),
                    'price' => $book->getPrice(),
                    'cover' => $book->getCover()
                ]
            );
        }
    }
}