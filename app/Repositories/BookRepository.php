<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Database\EntityManager;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use PDO;

class BookRepository {
    private EntityManager $em;
    private $pdo;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function findAll(): array {
        $stmt = $this->em->query("SELECT * FROM books");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($data)) {
            throw new \RuntimeException("No books found in database");
        }

        $books = [];
        foreach ($data as $item) {
            $book = new Book($item);
            $this->loadCategories($book);
            $this->loadUsers($book);
            $books[] = $book;
        }

        return $books;
    }

    public function findById($id): ?Book
    {
        $id = (int)$id;
        $stmt = $this->em->query("SELECT * FROM books WHERE id = :id", ['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $book = new Book($data);
        $this->loadCategories($book);

        return $book;
    }

    public function save(Book $book): void {
        $this->em->beginTransaction();

        $currentTime = date('Y-m-d H:i:s');
        $data = [
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'price' => $book->getPrice(),
            'cover' => $book->getCover(),
            'updated_at' => $currentTime
        ];

        if ($book->getId()) {
            $data['id'] = $book->getId();
            $this->em->query(
                "UPDATE books SET 
                title = :title, 
                author = :author, 
                price = :price, 
                cover = :cover,
                updated_at = :updated_at 
                WHERE id = :id",
                $data
            );

            // Удаляем старые связи
            $this->em->query(
                "DELETE FROM book_categories WHERE book_id = :book_id",
                ['book_id' => $book->getId()]
            );
            $this->em->query(
                "DELETE FROM user_books WHERE book_id = :book_id",
                ['book_id' => $book->getId()]
            );
        } else {
            $data['created_at'] = $currentTime;
            $this->em->query(
                "INSERT INTO books 
                (title, author, price, cover, created_at, updated_at) 
                VALUES 
                (:title, :author, :price, :cover, :created_at, :updated_at)",
                $data
            );
            $book->setId((int)$this->em->lastInsertId());
        }

        foreach ($book->getCategories() as $category) {
            $this->em->query(
                "INSERT INTO book_categories (book_id, category_id) 
                VALUES (:book_id, :category_id)",
                [
                    'book_id' => $book->getId(),
                    'category_id' => $category->getId()
                ]
            );
        }

        foreach ($book->getUsers() as $user) {
            $this->em->query(
                "INSERT INTO user_books (user_id, book_id) 
                VALUES (:user_id, :book_id)",
                [
                    'user_id' => $user->getId(),
                    'book_id' => $book->getId()
                ]
            );
        }

        $this->em->commit();
    }

    public function findAllWithCategories(): array
    {
        $stmt = $this->em->query("
        SELECT b.*, 
               GROUP_CONCAT(c.id) AS category_ids,
               GROUP_CONCAT(c.name) AS category_names
        FROM books b
        LEFT JOIN book_categories bc ON b.id = bc.book_id
        LEFT JOIN categories c ON bc.category_id = c.id
        GROUP BY b.id
    ");

        $booksData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $books = [];
        foreach ($booksData as $data) {
            $book = new Book($data);
            $book->clearCategories();

            if (!empty($data['category_ids'])) {
                $categoryIds = explode(',', $data['category_ids']);
                $categoryNames = explode(',', $data['category_names']);

                foreach ($categoryIds as $index => $categoryId) {
                    $category = new Category([
                        'id' => $categoryId,
                        'name' => $categoryNames[$index] ?? 'Unknown'
                    ]);
                    $book->addCategory($category);
                }
            }

            $books[] = $book;
        }

        return $books;
    }

    public function loadCategories(Book $book): void {
        $book->clearCategories();
        $stmt = $this->em->query(
            "SELECT c.* FROM categories c
            JOIN book_categories bc ON c.id = bc.category_id
            WHERE bc.book_id = :book_id",
            ['book_id' => $book->getId()]
        );

        while ($categoryData = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $book->addCategory(new Category($categoryData));
        }
    }

    private function loadUsers(Book $book): void {
        $stmt = $this->em->query(
            "SELECT u.* FROM users u
            JOIN user_books ub ON u.id = ub.user_id
            WHERE ub.book_id = :book_id",
            ['book_id' => $book->getId()]
        );

        while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $book->addUser(new User($userData));
        }
    }
}