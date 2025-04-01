<?php
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

$dbConfig = require __DIR__.'/../config/Database.php';
$routes = require __DIR__.'/../config/Routes.php';

$entityManager = new App\Database\EntityManager($dbConfig);
$templateEngine = new App\Services\TemplateEngine();

return [
    'entityManager' => $entityManager,
    'templateEngine' => $templateEngine,
    'routes' => $routes
];