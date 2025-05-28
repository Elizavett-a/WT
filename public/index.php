<?php
declare(strict_types=1);

require __DIR__.'/../config/Routes.php';

$routes = Config\Routes::getRoutes();
$router = new App\Router($routes);
$router->dispatch($_SERVER['REQUEST_URI'] ?? '/');