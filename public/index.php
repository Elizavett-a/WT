<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../config/Routes.php';

session_start();

ob_start();

try {
    $routes = Config\Routes::getRoutes();
    $router = new App\Router($routes);
    $router->dispatch($_SERVER['REQUEST_URI'] ?? '/');
} catch (Throwable $e) {
    ob_end_clean();
    http_response_code(500);
    error_log('Error: ' . $e->getMessage());
    header('Content-Type: text/html; charset=UTF-8');
    echo '<h1>Application Error</h1>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    exit;
}

ob_end_flush();