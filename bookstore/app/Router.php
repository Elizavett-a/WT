<?php
namespace app;

class Router {
    private $routes;

    public function __construct($routes) {
        $this->routes = $routes;
    }

    public function dispatch($uri) {
        foreach ($this->routes as $pattern => $route) {
            if (preg_match("#^$pattern$#", $uri, $matches)) {
                $controllerName = "App\\Controllers\\" . $route['controller'];
                $action = $route['action'];

                $entityManager = new EntityManager();
                $templateEngine = new Services\TemplateEngine();

                $controller = new $controllerName($entityManager, $templateEngine);

                $params = array_slice($matches, 1);
                call_user_func_array([$controller, $action], $params);

                return;
            }
        }

        header("HTTP/1.0 404 Not Found");
        echo '404 Not Found';
    }
}