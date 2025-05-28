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
        $book = $this->bookRepo->findById($id);

        if ($book) {
            $this->bookRepo->loadCategories($book);
        }

        return $book;
    }

    public function getAllBooks(): array
    {
        $books = $this->bookRepo->findAllWithCategories();

        foreach ($books as $book) {
            $this->bookRepo->loadCategories($book);
        }

        return $books;
    }

    public function updateBook(int $id, array $data): void
    {
        $book = $this->bookRepo->findById($id);

        if (!$book) {
            throw new \RuntimeException("Book not found");
        }

        $book->setTitle($data['title']);
        $book->setAuthor($data['author']);
        $book->setPrice($data['price']);

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