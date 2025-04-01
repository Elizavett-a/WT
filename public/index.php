<?php
// Загрузка ядра приложения
$app = require_once __DIR__.'/../app/bootstrap.php';

// Инициализация и запуск маршрутизатора
$router = new App\Router($app['routes']);
$router->dispatch($_SERVER['REQUEST_URI'] ?? '/');