<?php
declare(strict_types = 1);

namespace App\Controllers;

require_once __DIR__ . '/BaseController.php';

use App\Services\AdminService;
use App\TemplateEngine;
use JetBrains\PhpStorm\NoReturn;

class AdminController extends BaseController
{
    private AdminService $adminService;

    public function __construct(TemplateEngine $engine, AdminService $adminService)
    {
        parent::__construct($engine);
        $this->adminService = $adminService;
    }

    public function fileViewAction(string $path): void
    {
        try {
            $fullPath = $this->adminService->getSafePath($path);

            if (is_dir($fullPath)) {
                $this->showDirectory($fullPath, $path);
            } else {
                $this->showFile($fullPath, $path);
            }
        } catch (\Throwable $e) {
        }
    }

    public function createFileAction(): void
    {
        try {
            $name = $_POST['name'] ?? null;
            $this->adminService->createFile($name);
            $this->jsonResponse(['success' => true]);
        } catch (\Throwable $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function deleteAction(string $path): void
    {
        try {
            $this->adminService->delete($path);
        } catch (\Throwable $e) {
            exit;
        }

        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/bookstore/public/admin';
        header("Location: " . $redirectUrl);
        exit;
    }

    public function createDirectoryAction(): void
    {
        try {
            $name = $_POST['name'] ?? null;
            $this->adminService->createDirectory($name);
            $this->jsonResponse(['success' => true]);
        } catch (\Throwable $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function indexAction(): void
    {
        $this->render('admin/file_manager.tpl');
    }

    public function listAction(): void
    {
        try {
            $requestedDir = $_GET['dir'] ?? '';
            $currentDir = $this->adminService->getSafePath($requestedDir);

            $files = $this->adminService->scanDirectory($currentDir);

            $this->render('admin/file_manager.tpl', [
                'currentDir' => $requestedDir,
                'files' => $files,
            ]);
        } catch (\Throwable $e) {
        }
    }

    public function updateAction(): void
    {
        try {
            $path = $_POST['path'] ?? '';
            $content = $_POST['content'] ?? null;
            $newName = $_POST['name'] ?? null;

            $this->adminService->updateFile($path, $content, $newName);
            $this->jsonResponse(['success' => true]);
        } catch (\Throwable $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function showDirectory(string $fullPath, string $relativePath): void
    {
        $files = $this->adminService->scanDirectory($fullPath);

        $this->render('admin/file_manager.tpl', [
            'currentDir' => $relativePath ?: '/',
            'files' => $files,
        ]);
    }

    private function showFile(string $fullPath, string $relativePath): void
    {
        $content = $this->adminService->getFileContent($relativePath);

        $this->render('admin/file_view.tpl', [
            'content' => htmlspecialchars($content),
            'backUrl' => '/admin/view/' . dirname($relativePath)
        ]);
    }

    #[NoReturn]
    private function jsonResponse(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    #[NoReturn]
    private function redirectToDirectory(string $path): void
    {
        header("Location: /admin/files?dir=" . $path);
        exit;
    }
}