<?php
namespace App\Controllers;

use App\Services\TemplateEngine;

class AdminController extends BaseController {
    private string $basePath;
    private array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'css', 'js', 'tpl', 'html', 'htm'];
    private array $allowedDirectories = ['assets', 'templates'];

    public function __construct(TemplateEngine $templateEngine) {
        parent::__construct($templateEngine);
        $this->basePath = realpath(__DIR__ . '/../../public');
    }

    public function listAction(): void {
        $path = $this->getRequestedPath();
        $this->render('admin/filemanager.tpl', [
            'currentPath' => $path,
            'parentPath' => dirname($path),
            'items' => $this->getDirectoryItems($path)
        ]);
    }

    public function viewAction($id = null): void {
        $filePath = $this->getRequestedPath();

        if (!file_exists($filePath) || is_dir($filePath)) {
            header("Location: /bookstore/public/admin");
            exit;
        }

        if ($this->isTextFile($filePath)) {
            $content = file_get_contents($filePath);
            $this->render('admin/filemanager.tpl', [
                'fileContent' => $content,
                'currentPath' => $filePath,
                'isEditMode' => true
            ]);
        } else {
            $this->downloadFile($filePath);
        }
    }

    public function editAction(): void {
        $filePath = $this->getRequestedPath();
        $content = file_get_contents($filePath);
        file_put_contents($filePath, $_POST['content']);
        header("Location: /bookstore/public/admin/view?path=" . urlencode($filePath));
    }

    public function deleteAction(): void {
        $path = $this->getRequestedPath();

        if (is_dir($path)) {
            rmdir($path);
        } else {
            unlink($path);
        }

        header("Location: /bookstore/public/admin");
    }

    public function uploadAction(): void {
        $targetDir = $this->getRequestedPath();

        if (isset($_FILES['file'])) {
            $targetFile = $targetDir . '/' . basename($_FILES['file']['name']);

            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
                header("Location: /bookstore/public/admin");
                exit;
            }
        }

        http_response_code(400);
        echo "File upload failed";
    }

    public function createFolderAction(): void {
        $path = $this->getRequestedPath();
        $folderName = $_POST['folder_name'];
        $newPath = $path . '/' . $folderName;

        if (!file_exists($newPath)) {
            mkdir($newPath);
        }

        header("Location: /bookstore/public/admin");
    }

    private function getRequestedPath(): string {
        $requestPath = $_GET['path'] ?? '';
        $fullPath = $this->basePath . '/' . ltrim($requestPath, '/');
        $fullPath = realpath($fullPath) ?: $this->basePath;

        // Проверяем, что путь находится в разрешенных директориях
        foreach ($this->allowedDirectories as $allowedDir) {
            if (strpos($fullPath, $this->basePath . '/' . $allowedDir) === 0) {
                return $fullPath;
            }
        }

        return $this->basePath;
    }

    private function getDirectoryItems(string $path): array {
        $items = [];

        if ($path !== $this->basePath) {
            $items[] = [
                'name' => '..',
                'path' => dirname(substr($path, strlen($this->basePath) + 1)),
                'is_dir' => true
            ];
        }

        foreach (scandir($path) as $item) {
            if ($item === '.' || $item === '..') continue;

            $itemPath = $path . '/' . $item;
            $relativePath = substr($itemPath, strlen($this->basePath) + 1);

            $items[] = [
                'name' => $item,
                'path' => $relativePath,
                'is_dir' => is_dir($itemPath),
                'extension' => pathinfo($item, PATHINFO_EXTENSION),
                'size' => filesize($itemPath),
                'modified' => date("Y-m-d H:i:s", filemtime($itemPath))
            ];
        }

        return $items;
    }

    private function isTextFile(string $filePath): bool {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        return in_array($extension, ['css', 'js', 'tpl', 'html', 'htm', 'txt', 'php']);
    }

    private function downloadFile(string $filePath): void {
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
}