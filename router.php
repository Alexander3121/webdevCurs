<?php
/**
 * Только для локального запуска встроенным сервером PHP (заменяет .htaccess):
 *   cd autoprokat
 *   php -S localhost:8000 router.php
 */

$uri  = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$path = __DIR__ . $uri;

// Закрываем служебные папки и файлы
if (preg_match('~^/(src|templates|sql)(/|$)|^/(config|config\.local)\.php$~', $uri)) {
    http_response_code(403);
    echo 'Forbidden';
    return true;
}

// Статика (css, js, svg) — отдаёт сам сервер
if ($uri !== '/' && is_file($path)) {
    return false;
}

$_GET['route'] = ltrim($uri, '/');
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';
return true;
