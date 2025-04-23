<?php
declare(strict_types = 1);

namespace App\Controllers;

require_once __DIR__ . '/BaseController.php';

use App\TemplateEngine;
use JetBrains\PhpStorm\NoReturn;

class AdminController extends BaseController
{
    private const BASE_DIR = '/var/www/bookstore/public/assets/css';

    public function __construct(TemplateEngine $engine) {
        parent::__construct($engine);
    }
    public function fileViewAction(string $path): void
    {
        try {
            $fullPath = $this->getSafePath($path);

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
            $targetDir = self::BASE_DIR;
            if (!is_dir($targetDir)) {
                throw new \RuntimeException("Директория не существует: ");
            }
            $name = $_POST['name'] ?? null;
            if (!$name) {
                throw new \RuntimeException("Имя файла не указано");
            }
            if (str_contains($name, '..')) {
                throw new \RuntimeException("Недопустимое имя файла");
            }
            $filePath = $targetDir . '/' . $name;
            if (file_exists($filePath)) {
                throw new \RuntimeException("Файл уже существует");
            }
            if (file_put_contents($filePath, '') === false) {
                throw new \RuntimeException("Ошибка при создании файла");
            }
            $this->jsonResponse(['success' => true]);
        } catch (\Throwable $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function deleteAction(string $path): void
    {
        try {
            $fullPath = $this->getSafePath($path);

            if (!file_exists($fullPath)) {
                throw new \RuntimeException("Элемент не найден: " . $path);
            }

            if (is_dir($fullPath)) {
                $this->deleteDirectory($fullPath);
            } else {
                if (!unlink($fullPath)) {
                    throw new \RuntimeException("Не удалось удалить файл: " . $path);
                }
            }
        } catch (\Throwable $e) {
            exit;
        }

        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/bookstore/public/admin';
        header("Location: " . $redirectUrl);
        exit;
    }

    private function deleteDirectory(string $dir): bool
    {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $itemPath = $dir . DIRECTORY_SEPARATOR . $item;
            if (!$this->deleteDirectory($itemPath)) {
                return false;
            }
        }
        return rmdir($dir);
    }

    #[NoReturn] private function jsonResponse(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function createDirectoryAction(): void
    {
        try {
            $targetDir = self::BASE_DIR;
            if (!is_dir($targetDir)) {
                throw new \RuntimeException("Директория не существует: ");
            }
            $name = $_POST['name'] ?? null;
            if (!$name) {
                throw new \RuntimeException("Имя директории не указано");
            }
            if (str_contains($name, '..')) {
                throw new \RuntimeException("Недопустимое имя директории");
            }
            $dirPath = $targetDir . '/' . $name;
            if (file_exists($dirPath)) {
                throw new \RuntimeException("Директория уже существует");
            }
            if (!mkdir($dirPath, 0777, true)) {
                throw new \RuntimeException("Не удалось создать директорию");
            }
            $this->jsonResponse(['success' => true]);
        } catch (\Throwable $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function indexAction(): void
    {
        $this->render('admin/file_manager.tpl');
    }

    private function showDirectory(string $fullPath, string $relativePath): void
    {
        $files = $this->scanDir($fullPath);

        $this->render('admin/file_manager.tpl', [
            'currentDir' => $relativePath ?: '/',
            'files' => $files,
        ]);
    }

    private function showFile(string $fullPath, string $relativePath): void
    {
        if (!file_exists($fullPath)) {
            throw new \RuntimeException("Файл не найден: " . $relativePath);
        }

        $content = file_get_contents($fullPath);
        $this->render('admin/file_view.tpl', [
            'content' => htmlspecialchars($content),
            'backUrl' => '/admin/view/' . dirname($relativePath)
        ]);
    }

    public function updateAction(): void
    {
        try {
            $fullPath = self::BASE_DIR;

            if (!is_file($fullPath)) {
                throw new \RuntimeException("Файл не найден: ");
            }

            $content = $_POST['content'] ?? null;
            $newName = $_POST['name'] ?? null;

            if ($content === null) {
                throw new \RuntimeException("Содержимое файла не передано");
            }
            if ($newName !== null && trim($newName) !== '' && $newName !== basename($fullPath)) {
                $dir = dirname($fullPath);
                $newFullPath = $dir . DIRECTORY_SEPARATOR . $newName;

                if (file_exists($newFullPath)) {
                    throw new \RuntimeException("Новый файл с именем '{$newName}' уже существует");
                }

                if (!rename($fullPath, $newFullPath)) {
                    throw new \RuntimeException("Не удалось переименовать файл");
                }

                $fullPath = $newFullPath;
            }

            if (file_put_contents($fullPath, $content) === false) {
                throw new \RuntimeException("Ошибка при записи файла");
            }

            $this->jsonResponse(['success' => true]);
        } catch (\Throwable $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    #[NoReturn]
    private function redirectToDirectory(string $path): void
    {
        header("Location: /admin/files?dir=" . $path);
        exit;
    }

    private function scanDir(string $path): array
    {
        $files = [];
        foreach (scandir($path) as $item) {
            if ($item === '.' || $item === '..') continue;

            $fullPath = $path . '/' . $item;
            $isDir = is_dir($fullPath);

            $files[] = [
                'name' => $item,
                'path' => $this->getRelativePath($fullPath),
                'is_dir' => $isDir,
                'size' => $isDir ? '-' : $this->formatSize(filesize($fullPath)),
                'modified' => date('Y-m-d H:i:s', filemtime($fullPath)),
            ];
        }
        return $files;
    }

    private function getRelativePath(string $fullPath): string
    {
        $baseDir = realpath(self::BASE_DIR);
        return ltrim(str_replace($baseDir, '', $fullPath), '/');
    }

    public function listAction(): void
    {
        try {
            $requestedDir = $_GET['dir'] ?? '';
            $currentDir = $this->getSafePath($requestedDir);

            if (!is_dir($currentDir)) {
                throw new \RuntimeException("Недопустимая директория: " . $requestedDir);
            }

            $files = $this->scanDir($currentDir);

            $this->render('admin/file_manager.tpl', [
                'currentDir' => $requestedDir,
                'files' => $files,
            ]);
        } catch (\Throwable $e) {
        }
    }

    private function getSafePath(string $path): string
    {
        $baseDir = realpath(self::BASE_DIR);
        $cleanPath = ltrim($path, '/');

        if (str_contains($cleanPath, '..')) {
            throw new \RuntimeException("Недопустимый путь");
        }

        $fullPath = $baseDir . '/' . $cleanPath;
        $realPath = realpath($fullPath);

        if (!$realPath || !str_starts_with($realPath, $baseDir)) {
            throw new \RuntimeException("Доступ запрещен: " . $path);
        }

        return $realPath;
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }
}
