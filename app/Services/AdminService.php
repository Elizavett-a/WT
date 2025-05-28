<?php
declare(strict_types = 1);

namespace App\Services;

class AdminService
{
    private const BASE_DIR = '/var/www/bookstore/public/assets/css';

    public function getSafePath(string $path): string
    {
        $baseDir = $this->getBaseDir();
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

    public function createFile(string $name): void
    {
        $targetDir = self::BASE_DIR;
        if (!is_dir($targetDir)) {
            throw new \RuntimeException("Директория не существует: ");
        }

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
    }

    public function delete(string $path): void
    {
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
    }

    public function deleteDirectory(string $dir): bool
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

    public function createDirectory(string $name): void
    {
        $targetDir = self::BASE_DIR;
        if (!is_dir($targetDir)) {
            throw new \RuntimeException("Директория не существует: ");
        }

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
    }

    public function updateFile(string $path, string $content, ?string $newName = null): void
    {
        $fullPath = $this->getSafePath($path);

        if (!is_file($fullPath)) {
            throw new \RuntimeException("Файл не найден: ");
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
    }

    public function scanDirectory(string $path): array
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

    public function getFileContent(string $path): string
    {
        $fullPath = $this->getSafePath($path);

        if (!file_exists($fullPath)) {
            throw new \RuntimeException("Файл не найден: " . $path);
        }

        return file_get_contents($fullPath);
    }

    private function getRelativePath(string $fullPath): string
    {
        $baseDir = $this->getBaseDir();
        return ltrim(str_replace($baseDir, '', $fullPath), '/');
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

    private function getBaseDir(): string
    {
        return realpath(self::BASE_DIR);
    }
}