<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Database\EntityManager;
use App\Models\Category;
use PDO;

class CategoryRepository {
    private EntityManager $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function findAll(): array {
        $stmt = $this->em->query("SELECT * FROM categories");
        $categories = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = new Category($data);
        }
        return $categories;
    }

    public function findById(int $id): ?Category {
        $stmt = $this->em->query("SELECT * FROM categories WHERE id = :id", ['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new Category($data) : null;
    }

    public function save(Category $category): void {
        $data = $category->toArray();

        if ($category->getId()) {
            $this->em->query(
                "UPDATE categories SET 
                name = :name, 
                description = :description,
                updated_at = :updated_at 
                WHERE id = :id",
                $data
            );
        } else {
            unset($data['id']);
            $this->em->query(
                "INSERT INTO categories 
                (name, description, created_at, updated_at) 
                VALUES 
                (:name, :description, :created_at, :updated_at)",
                $data
            );
            $category->setId((int)$this->em->lastInsertId());
        }
    }

    public function delete(int $id): bool {
        $stmt = $this->em->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function getBooksWithNullCategory(): array {
        $stmt = $this->em->query("
            SELECT b.* FROM books b
            JOIN book_categories bc ON b.id = bc.book_id
            WHERE bc.category_id IS NULL
        ");

        $books = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $books[] = $data;
        }
        return $books;
    }
}