<?php
namespace App;

class Router {
    private array $routes;

    public function __construct(array $routes) {
        $this->routes = $routes;
    }

    public function dispatch(string $uri): void {
        // Нормализация URI
        $uri = $this->normalizeUri($uri);
        error_log("Processing URI: " . $uri);

        foreach ($this->routes as $pattern => $route) {
            // Динамические параметры типа {id}
            if (str_contains($pattern, '{id}')) {
                $basePattern = str_replace('/{id}', '', $pattern);
                if (str_starts_with($uri, $basePattern.'/')) {
                    $id = substr($uri, strlen($basePattern) + 1);
                    if (is_numeric($id)) {
                        $this->executeControllerAction($route, $id);
                        return;
                    }
                }
            }

            // Точное совпадение
            $normalizedPattern = $this->normalizeUri($pattern);
            if ($uri === $normalizedPattern) {
                $this->executeControllerAction($route);
                return;
            }
        }

        http_response_code(404);
        error_log("No route found for: " . $uri);
        echo '404 Not Found - No matching route for: '.htmlspecialchars($uri);
    }

    private function normalizeUri(string $uri): string {
        // Удаляем базовый путь если он есть
        $basePath = '/bookstore/public';
        $path = parse_url($uri, PHP_URL_PATH);

        // Если путь начинается с basePath
        if (str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
        }

        return '/' . trim($path, '/');
    }

    private function executeControllerAction(array $route, $id = null): void {
        try {
            $controllerName = "App\\Controllers\\" . $route['controller'];
            $action = $route['action'];

            if (!class_exists($controllerName)) {
                throw new \RuntimeException("Controller $controllerName not found");
            }

            if (!method_exists($controllerName, $action)) {
                throw new \RuntimeException("Action $action not found in $controllerName");
            }

            $controller = new $controllerName(
                new \App\Services\BookService(new \App\Repositories\BookRepository(
                    new \App\Database\EntityManager(require __DIR__ . '/../config/Database.php')
                )),
                new \App\Services\TemplateEngine()
            );

            $id !== null ? $controller->$action($id) : $controller->$action();

        } catch (\Exception $e) {
            error_log("Controller error: " . $e->getMessage());
            http_response_code(500);
            echo '500 Server Error - '.htmlspecialchars($e->getMessage());
        }
    }
}