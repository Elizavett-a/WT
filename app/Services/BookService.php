<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\BookRepository;
use App\Models\Book;

class BookService {
    private BookRepository $bookRepo;

    public function __construct(BookRepository $bookRepo) {
        $this->bookRepo = $bookRepo;
    }

    public function getBookById(int $id): ?Book
    {
        return $this->bookRepo->findById((int)$id);
    }

    public function getAllBooks(): array
    {
        return $this->bookRepo->findAll();
    }

    public function updateBook(int $id, array $data): void
    {
        $book = $this->bookRepo->findById($id);

        if (!$book) {
            throw new \RuntimeException("Book not found");
        }

        // Обновляем данные
        $book->setTitle($data['title']);
        $book->setAuthor($data['author']);
        $book->setPrice($data['price']);

        // Обработка загрузки новой обложки
        if (!empty($data['cover']['tmp_name'])) {
            $newFilename = $this->uploadCover($data['cover']);
            $book->setCover($newFilename);
        }

        $this->bookRepo->save($book);
    }

    private function uploadCover(array $file): string
    {
        $uploadDir = __DIR__ . '/../../public/assets/images/';
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('cover_') . '.' . $extension;
        $destination = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \RuntimeException("Failed to upload cover image");
        }

        return $filename;
    }
}