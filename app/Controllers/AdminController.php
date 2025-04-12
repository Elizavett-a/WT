<?php
namespace App\Controllers;

use App\Services\TemplateEngine;

class AdminController extends BaseController {
    private string $basePath;
    private array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'css', 'js', 'tpl', 'html', 'htm', 'txt', 'php'];
    private array $allowedDirectories = ['assets', 'templates'];

    public function __construct(TemplateEngine $templateEngine) {
        parent::__construct($templateEngine);
        $this->basePath = realpath(__DIR__ . '/../../public');
    }

    public function listAction($path = null): void
    {
        try {
            $requestedPath = $path ?? $_GET['path'] ?? '';
            $fullPath = $this->getValidatedPath($requestedPath);

            $this->render('admin/filemanager.tpl', [
                'currentPath' => $this->getRelativePath($fullPath),
                'items' => $this->getDirectoryItems($fullPath),
                'isEditMode' => false
            ]);
        } catch (\RuntimeException $e) {
            $this->showError($e->getMessage());
        }
    }

    private function getRelativePath(string $fullPath): string
    {
        return ltrim(str_replace($this->basePath, '', $fullPath), '/');
    }

    private function getDirectoryItems(string $path): array
    {
        if (!is_readable($path)) {
            throw new \RuntimeException("Нет прав на чтение директории");
        }

        $items = [];

        // Добавляем переход на уровень выше
        if ($path !== $this->basePath) {
            $parentPath = dirname($path);
            $items[] = [
                'name' => '..',
                'path' => $this->getRelativePath($parentPath),
                'is_dir' => true,
                'size' => '-',
                'modified' => '-'
            ];
        }

        // Сканируем директорию
        $scanResult = scandir($path);
        if ($scanResult === false) {
            throw new \RuntimeException("Ошибка чтения директории");
        }

        foreach ($scanResult as $item) {
            if ($item === '.' || $item === '..') continue;

            $itemPath = $path . '/' . $item;

            $items[] = [
                'name' => $item,
                'path' => $this->getRelativePath($itemPath),
                'is_dir' => is_dir($itemPath),
                'size' => is_dir($itemPath) ? '-' : $this->formatFileSize(filesize($itemPath)),
                'modified' => date("Y-m-d H:i:s", filemtime($itemPath)),
                'readable' => is_readable($itemPath),
                'writable' => is_writable($itemPath)
            ];
        }

        return $items;
    }
    private function getValidatedPath(string $relativePath): string
    {
        $fullPath = realpath($this->basePath . '/' . ltrim($relativePath, '/'));

        if ($fullPath === false) {
            throw new \RuntimeException("Указанный путь не существует");
        }

        if (!str_starts_with($fullPath, $this->basePath)) {
            throw new \RuntimeException("Доступ за пределы корневой директории запрещен");
        }

        return $fullPath;
    }
    public function viewAction($id = null): void {
        try {
            // Если $id передан, используем его для формирования пути
            $filePath = $id ? $this->basePath . '/' . ltrim($id, '/') : $this->getRequestedPath();

            if (!$this->checkPathAccess($filePath)) {
                $this->showError("Недостаточно прав для доступа к этому файлу");
                return;
            }

            if (!file_exists($filePath) || is_dir($filePath)) {
                $this->redirectWithMessage('/bookstore/public/admin', 'Файл не найден', 'error');
                return;
            }

            if (!$this->isTextFile($filePath)) {
                $this->downloadFile($filePath);
                return;
            }

            if (!is_readable($filePath)) {
                $this->showError("Нет прав на чтение файла");
                return;
            }

            $content = file_get_contents($filePath);
            $this->render('admin/filemanager.tpl', [
                'fileContent' => $content,
                'currentPath' => $this->getRelativePath($filePath),
                'isEditMode' => true
            ]);
        } catch (\Exception $e) {
            $this->showError($e->getMessage());
        }
    }

    public function editAction(): void {
        try {
            $filePath = $this->getRequestedPath();

            if (!$this->checkPathAccess($filePath)) {
                $this->showError("Недостаточно прав для редактирования файла");
                return;
            }

            if (!is_writable($filePath)) {
                $this->redirectWithMessage(
                    '/bookstore/public/admin/view?path=' . urlencode($this->getRelativePath($filePath)),
                    'Нет прав на запись в файл',
                    'error'
                );
                return;
            }

            file_put_contents($filePath, $_POST['content']);
            $this->redirectWithMessage(
                '/bookstore/public/admin/view?path=' . urlencode($this->getRelativePath($filePath)),
                'Файл успешно сохранен',
                'success'
            );
        } catch (\Exception $e) {
            $this->showError($e->getMessage());
        }
    }

    public function deleteAction(): void {
        try {
            $path = $this->getRequestedPath();

            if (!$this->checkPathAccess($path)) {
                $this->showError("Недостаточно прав для удаления");
                return;
            }

            if (!is_writable(dirname($path))) {
                $this->redirectWithMessage('/bookstore/public/admin', 'Нет прав на удаление', 'error');
                return;
            }

            if (is_dir($path)) {
                if (!@rmdir($path)) {
                    $this->redirectWithMessage('/bookstore/public/admin', 'Не удалось удалить директорию (возможно, она не пуста)', 'error');
                    return;
                }
            } else {
                if (!@unlink($path)) {
                    $this->redirectWithMessage('/bookstore/public/admin', 'Не удалось удалить файл', 'error');
                    return;
                }
            }

            $this->redirectWithMessage('/bookstore/public/admin', 'Удаление выполнено успешно', 'success');
        } catch (\Exception $e) {
            $this->showError($e->getMessage());
        }
    }

    public function uploadAction(): void {
        try {
            $targetDir = $this->getRequestedPath();

            if (!$this->checkPathAccess($targetDir)) {
                $this->showError("Недостаточно прав для загрузки файлов");
                return;
            }

            if (!is_writable($targetDir)) {
                $this->redirectWithMessage('/bookstore/public/admin', 'Нет прав на запись в директорию', 'error');
                return;
            }

            if (empty($_FILES['file']['tmp_name'])) {
                $this->redirectWithMessage('/bookstore/public/admin', 'Файл не был загружен', 'error');
                return;
            }

            $targetFile = $targetDir . '/' . basename($_FILES['file']['name']);

            if (file_exists($targetFile)) {
                $this->redirectWithMessage('/bookstore/public/admin', 'Файл с таким именем уже существует', 'error');
                return;
            }

            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
                $this->redirectWithMessage('/bookstore/public/admin', 'Файл успешно загружен', 'success');
            } else {
                $this->redirectWithMessage('/bookstore/public/admin', 'Ошибка при загрузке файла', 'error');
            }
        } catch (\Exception $e) {
            $this->showError($e->getMessage());
        }
    }

    public function createFolderAction(): void {
        try {
            $path = $this->getRequestedPath();

            if (!$this->checkPathAccess($path)) {
                $this->showError("Недостаточно прав для создания директории");
                return;
            }

            if (!is_writable($path)) {
                $this->redirectWithMessage('/bookstore/public/admin', 'Нет прав на создание директории', 'error');
                return;
            }

            $folderName = trim($_POST['folder_name']);
            if (empty($folderName)) {
                $this->redirectWithMessage('/bookstore/public/admin', 'Имя директории не может быть пустым', 'error');
                return;
            }

            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $folderName)) {
                $this->redirectWithMessage('/bookstore/public/admin', 'Недопустимое имя директории', 'error');
                return;
            }

            $newPath = $path . '/' . $folderName;

            if (file_exists($newPath)) {
                $this->redirectWithMessage('/bookstore/public/admin', 'Директория уже существует', 'error');
                return;
            }

            if (!mkdir($newPath, 0755)) {
                $this->redirectWithMessage('/bookstore/public/admin', 'Не удалось создать директорию', 'error');
                return;
            }

            $this->redirectWithMessage('/bookstore/public/admin', 'Директория успешно создана', 'success');
        } catch (\Exception $e) {
            $this->showError($e->getMessage());
        }
    }

    private function getRequestedPath(): string {
        $requestPath = $_GET['path'] ?? '';
        $fullPath = $this->basePath . '/' . ltrim($requestPath, '/');
        $fullPath = realpath($fullPath) ?: $this->basePath;

        // Проверяем, что путь находится в разрешенных директориях
        foreach ($this->allowedDirectories as $allowedDir) {
            if (str_starts_with($fullPath, $this->basePath . '/' . $allowedDir)) {
                return $fullPath;
            }
        }

        return $this->basePath;
    }

    private function checkPathAccess(string $path): bool {
        // Проверка, что путь находится внутри базового пути
        if (!str_starts_with($path, $this->basePath)) {
            return false;
        }

        // Проверка разрешенных директорий
        foreach ($this->allowedDirectories as $allowedDir) {
            if (str_starts_with($path, $this->basePath . '/' . $allowedDir)) {
                return true;
            }
        }

        return false;
    }

    private function isTextFile(string $filePath): bool {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        return in_array($extension, $this->allowedExtensions);
    }

    private function formatFileSize(int $bytes): string {
        if ($bytes < 1024) return $bytes . ' B';
        elseif ($bytes < 1048576) return round($bytes / 1024, 2) . ' KB';
        else return round($bytes / 1048576, 2) . ' MB';
    }

    private function downloadFile(string $filePath): void {
        if (!is_readable($filePath)) {
            $this->showError("Нет прав на чтение файла");
            return;
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($filePath).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    private function redirectWithMessage(string $url, string $message, string $type = 'info'): void {
        header("Location: $url?message=" . urlencode($message) . "&message_type=$type");
        exit;
    }

    private function showError(string $message): void {
        $this->render('admin/error.tpl', [
            'error_message' => $message
        ]);
    }
}