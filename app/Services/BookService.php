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
        return $this->bookRepo->findById($id);
    }

    public function getAllBooks(): array
    {
        return $this->bookRepo->findAll();
    }
}