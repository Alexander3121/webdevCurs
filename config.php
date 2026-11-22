<?php
/**
 * Настройки приложения.
 * Свои доступы к БД положи в config.local.php (он не попадает в git).
 */

$config = [
    'app_name' => 'АвтоПрокат',
    'author'   => 'Пронин А. В., группа 253-321',

    'db' => [
        'host'     => 'localhost',
        'name'     => 'autoprokat',
        'user'     => 'root',
        'password' => '',
    ],

    'per_page' => [
        'cars'     => 6,
        'articles' => 4,
    ],
];

if (is_file(__DIR__ . '/config.local.php')) {
    // config.local.php возвращает массив, который накладывается поверх настроек выше
    $config = array_replace_recursive($config, require __DIR__ . '/config.local.php');
}

return $config;
