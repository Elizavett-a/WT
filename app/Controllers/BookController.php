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

    public function viewAction($id): void {
        error_log("Запрошен ID книги: " . $id);
        $book = $this->bookService->getBookById($id);
        if (!$book) {
            error_log("Книга с ID $id не найдена в базе");
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

    public function editAction($id): void
    {
        $book = $this->bookService->getBookById((int)$id);

        if (!$book) {
            http_response_code(404);
            echo 'Книга не найдена';
            return;
        }

        $this->render('books/edit.tpl', [
            'book' => $book,
            'errors' => $_SESSION['form_errors'] ?? null
        ]);

        unset($_SESSION['form_errors']);
    }

    public function updateAction($id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Метод не разрешен';
            return;
        }

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'author' => trim($_POST['author'] ?? ''),
            'price' => (float)$_POST['price'] ?? 0,
            'cover' => $_FILES['cover'] ?? null
        ];

        $errors = $this->validateBookData($data);

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            header("Location: /bookstore/public/books/edit/$id");
            exit;
        }

        try {
            $this->bookService->updateBook((int)$id, $data);
            header("Location: /bookstore/public/books/view/$id");
        } catch (\Exception $e) {
            http_response_code(500);
            echo 'Ошибка при обновлении книги';
        }
    }

    private function validateBookData(array $data): array
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors['title'] = 'Название обязательно';
        }

        if (empty($data['author'])) {
            $errors['author'] = 'Автор обязателен';
        }

        if ($data['price'] <= 0) {
            $errors['price'] = 'Цена должна быть положительной';
        }

        return $errors;
    }
}