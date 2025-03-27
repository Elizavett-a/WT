<?php
require_once __DIR__.'/../app/Services/TemplateEngine.php';
require_once __DIR__.'/../app/Controllers/BookController.php';


$router = new app\Router(require __DIR__.'/../config/routers.php');
$router->dispatch($_SERVER['REQUEST_URI']);