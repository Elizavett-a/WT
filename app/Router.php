<?php
declare(strict_types=1);

namespace App;

use App\Services\UserService;
use App\Services\MailService;

class Router {
    private const BASE_PATH = '/bookstore/public';
    private const CONTROLLER_NAMESPACE = "App\\Controllers\\";
    private const DATABASE_CONFIG_PATH = __DIR__ . '/../config/Database.php';

    private const PARAM_PATTERN = '/\\\{(\w+)(?::(.+?))?\\\}/';

    private array $routes;
    private UserService $userService;

    public function __construct(array $routes, UserService $userService) {
        $this->routes = $routes;
        $this->userService = $userService;
    }

    public function dispatch(string $uri): void {
        if (isset($_COOKIE['remember_token']) && !isset($_SESSION['user'])) {
            $user = $this->userService->validateRememberToken($_COOKIE['remember_token']);
            if ($user) {
                $_SESSION['user'] = [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail()
                ];
            }
        }

        $uri = $this->normalizeUri($uri);

        foreach ($this->routes as $pattern => $route) {
            if ($this->matchRoute($pattern, $uri, $route)) {
                return;
            }
        }

        $this->sendNotFoundResponse($uri);
    }

    private function matchRoute(string $pattern, string $uri, array $route): bool {
        $regex = $this->patternToRegex($pattern);

        if (preg_match($regex, $uri, $matches)) {
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            $this->executeControllerAction($route, $params);
            return true;
        }

        return false;
    }

    private function patternToRegex(string $pattern): string {
        $pattern = preg_quote($this->normalizeUri($pattern), '/');
        $pattern = preg_replace_callback(
            self::PARAM_PATTERN,
            fn($m) => '(?<'.$m[1].'>'.($m[2] ?? '[^/]+').')',
            $pattern
        );
        return '@^'.$pattern.'$@D';
    }

    private function normalizeUri(string $uri): string {
        $path = parse_url(rawurldecode($uri), PHP_URL_PATH);

        if (str_starts_with($path, self::BASE_PATH)) {
            $path = substr($path, strlen(self::BASE_PATH));
        }

        return '/' . trim(urldecode($path), '/');
    }

    private function executeControllerAction(array $route, array $params): void {
        try {
            $controllerName = self::CONTROLLER_NAMESPACE . $route['controller'];
            $this->validateController($controllerName, $route['action']);

            $controller = $this->createController($controllerName);
            $this->invokeControllerAction($controller, $controllerName, $route['action'], $params);

        } catch (\Exception $e) {
            $this->handleControllerError($e);
        }
    }

    private function validateController(string $controllerName, string $action): void {
        if (!class_exists($controllerName)) {
            throw new \RuntimeException("Controller $controllerName not found");
        }

        if (!method_exists($controllerName, $action)) {
            throw new \RuntimeException("Action $action not found in $controllerName");
        }
    }

    private function createController(string $controllerName) {
        $templateEngine = new TemplateEngine();
        $entityManager = new Database\EntityManager(require self::DATABASE_CONFIG_PATH);
        

        switch ($controllerName) {
            case self::CONTROLLER_NAMESPACE . 'AdminController':
                $adminService = new Services\AdminService();
                return new $controllerName($templateEngine, $adminService);

            case self::CONTROLLER_NAMESPACE . 'UserController':
                $userService = new Services\UserService(
                    new Repositories\UserRepository($entityManager)
                );
                $mailService = new Services\MailService();      
                return new $controllerName($templateEngine, $userService, $mailService);

            case self::CONTROLLER_NAMESPACE . 'BookController':
                $bookService = new Services\BookService(
                    new Repositories\BookRepository($entityManager)
                );
                return new $controllerName($templateEngine, $bookService);

            default:
                throw new \RuntimeException("Unknown controller type");
        }
    }

    private function invokeControllerAction(
        $controller,
        string $controllerName,
        string $action,
        array $params
    ): void {
        $method = new \ReflectionMethod($controllerName, $action);
        $args = $this->prepareMethodArguments($method, $params);
        $method->invokeArgs($controller, $args);
    }

    private function prepareMethodArguments(\ReflectionMethod $method, array $params): array {
        $args = [];

        foreach ($method->getParameters() as $param) {
            $paramName = $param->getName();

            if ($paramName === 'id' && isset($params['id'])) {
                $args[] = (int)$params['id'];
            } elseif ($paramName === 'token' && isset($params['token'])) {
                $args[] = $params['token'];
            } elseif (isset($params[$paramName])) {
                $args[] = $params[$paramName];
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } else {
                throw new \RuntimeException("Missing required parameter: $paramName");
            }
        }

        return $args;
    }

    private function sendNotFoundResponse(string $uri): void {
        http_response_code(404);
        echo '404 Not Found - No matching route for: '.htmlspecialchars($uri);
    }

    private function handleControllerError(\Exception $e): void {
        error_log("Controller error: " . $e->getMessage());
        http_response_code(500);
        echo '500 Server Error - '.htmlspecialchars($e->getMessage());
    }
}
