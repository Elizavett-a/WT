<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\BookService;
use App\Services\TemplateEngine;
use App\Models\Book;

class BookController extends BaseController {
    private BookService $bookService;

    public function __construct(BookService $bookService, TemplateEngine $engine) {
        parent::__construct($engine);
        $this->bookService = $bookService;
    }

    public function viewAction($id): void
    {
        $book = $this->bookService->getBookById($id);

        if (!$book instanceof Book) {
            http_response_code(404);
            echo 'Книга не найдена';
            return;
        }

        $this->render('books/view.tpl', ['book' => $book]);
    }

    public function listAction(): void
    {
        $books = $this->bookService->getAllBooks();
        $this->render('books/list.tpl', ['books' => $books]);
    }
}