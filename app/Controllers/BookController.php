<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use App\Services\BookService;
use App\Repositories\CategoryRepository;
use App\Database\EntityManager;
use App\TemplateEngine;

class BookController extends BaseController {
    private BookService $bookService;
    private CategoryRepository $categoryRepository;

    public function __construct(TemplateEngine $engine, BookService $bookService) {
        parent::__construct($engine);
        $this->bookService = $bookService;

        $entityManager = new EntityManager(require __DIR__ . '/../../config/Database.php');
        $this->categoryRepository = new CategoryRepository($entityManager);
    }

    public function vie1wAction($id): void {
        error_log("Запрошен ID книги: " . $id);
        $book = $this->bookService->getBookById($id);
        error_log("Categories for book {$id}: " . print_r($book->getCategories(), true));
        if (!$book) {
            error_log("Книга с ID $id не найдена в базе");
            http_response_code(404);
            echo 'Книга не найдена';
            return;
        }
        $this->render('books/view.tpl', ['book' => $book]);
    }

    public function viewAction($id): void {
        $book = $this->bookService->getBookById($id);

        $categories = array_map(function($category) {
            return [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'description' => $category->getDescription()
            ];
        }, $book->getCategories());

        $this->render('books/view.tpl', [
            'book' => $book,
            'categories' => $categories
        ]);
    }

    public function listAction(): void {
        $books = $this->bookService->getAllBooks();
        $this->render('books/list.tpl', ['books' => $books]);
    }

    public function editAction(int $id): void {
        $book = $this->bookService->getBookById($id);
        $allCategories = $this->categoryRepository->findAll();

        if (!$book) {
            http_response_code(404);
            echo 'Книга не найдена';
            return;
        }

        $categories = array_map(function($category) {
            return [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'description' => $category->getDescription()
            ];
        }, $book->getCategories());

        $this->render('books/edit.tpl', [
            'book' => $book,
            'book_categories' => $categories,
            'all_categories' => $allCategories
        ]);
    }

    public function updateAction($id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Метод не разрешен';
            return;
        }

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'author' => trim($_POST['author'] ?? ''),
            'price' => (float)($_POST['price'] ?? 0),
            'cover' => $_FILES['cover'] ?? null,
            'categories' => $_POST['categories'] ?? [],
            'new_category' => trim($_POST['new_category'] ?? '')
        ];

        try {
            if (!empty($data['new_category'])) {
                $newCategory = new Category(['name' => $data['new_category']]);
                $this->categoryRepository->save($newCategory);
                $data['categories'][] = $newCategory->getId();
            }

            $this->bookService->updateBook((int)$id, $data);
            header("Location: /bookstore/public/books/view/$id");
        } catch (\Exception $e) {
            error_log("Ошибка при обновлении книги: " . $e->getMessage());
            http_response_code(500);
            echo 'Ошибка при обновлении книги: ' . $e->getMessage();
        }
    }
}