<?php
// Загрузка ядра приложения
$app = require_once __DIR__ . '/../app/Bootstrap.php';

// Инициализация и запуск маршрутизатора
$router = new App\Router($app['routes']);
$router->dispatch($_SERVER['REQUEST_URI'] ?? '/');
error_log("Запрошенный URI: " . $_SERVER['REQUEST_URI']);