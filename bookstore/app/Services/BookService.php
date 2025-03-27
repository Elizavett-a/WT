<?php
namespace App\Services;

use App\Repositories\BookRepository;

class BookService {
    private $bookRepo;

    public function __construct(BookRepository $bookRepo) {
        $this->bookRepo = $bookRepo;
    }

    public function getFeaturedBooks(): array {
        return $this->bookRepo->findFeatured(12);
    }

    public function searchBooks(string $query): array {
        return $this->bookRepo->search($query);
    }
}