<?php
/**
 * Фронт-контроллер: все запросы попадают сюда (см. .htaccess / router.php).
 */

declare(strict_types=1);

session_start();

// Автозагрузка классов: App\Core\Router -> src/Core/Router.php
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (str_starts_with($class, $prefix)) {
        $file = __DIR__ . '/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) {
            require $file;
        }
    }
});

require __DIR__ . '/src/helpers.php';

$config = require __DIR__ . '/config.php';
App\Core\App::init($config, __DIR__);

// Адрес без базового пути и query string: "cars/5"
$route  = trim((string) ($_GET['route'] ?? ''), '/');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router = new App\Core\Router(require __DIR__ . '/src/routes.php');
$router->dispatch($method, $route);
