<?php
namespace App\Controllers;

use App\Services\BookService;
use App\Services\TemplateEngine;
class BookController extends BaseController {
    private $bookService;

    public function __construct(BookService $bookService, TemplateEngine $engine) {
        parent::__construct($engine);
        $this->bookService = $bookService;
    }

    public function viewAction($id) {
        $book = $this->bookService->getBookById($id);
        echo $this->render('books/view', ['book' => $book]);
    }

    public function listAction() {
        $books = $this->bookService->getAllBooks();
        echo $this->render('books/list', ['books' => $books]);
    }

    public function home() {
        $books = $this->bookService->getFeaturedBooks();
        echo $this->render('books/list', ['books' => $books]);
    }
}


namespace App\Controllers;

abstract class BaseController
{
    protected $entityManager;
    protected $templateEngine;

    public function __construct($entityManager, $templateEngine)
    {
        $this->entityManager = $entityManager;
        $this->templateEngine = $templateEngine;
    }

    abstract public function listAction();

    abstract public function viewAction($id);
}