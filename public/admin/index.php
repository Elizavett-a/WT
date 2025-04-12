<?php
require_once __DIR__.'/../../app/Bootstrap.php';

$dependencies = require __DIR__.'/../../app/Bootstrap.php';

// Создаем TemplateEngine
$templateEngine = new \App\Services\TemplateEngine();;

// Создаем AdminController с правильными зависимостями
$controller = new \App\Controllers\AdminController($templateEngine);

// Обрабатываем запрос
$path = $_GET['path'] ?? '';
$action = $_GET['action'] ?? 'listAction';

if (method_exists($controller, $action)) {
    $controller->$action($path);
} else {
    $controller->listAction();
}