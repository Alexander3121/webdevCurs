<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Глобальные данные приложения: конфиг, корневая папка, базовый URL.
 */
final class App
{
    private static array $config = [];
    private static string $root = '';
    private static string $baseUrl = '';

    public static function init(array $config, string $root): void
    {
        self::$config = $config;
        self::$root   = $root;

        // Если сайт лежит в подпапке (например /autoprokat), ссылки строятся от неё
        $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
        self::$baseUrl = rtrim($dir, '/');
    }

    /** config('db.host') */
    public static function config(string $key, mixed $default = null): mixed
    {
        $value = self::$config;
        foreach (explode('.', $key) as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return $default;
            }
            $value = $value[$part];
        }
        return $value;
    }

    public static function root(): string
    {
        return self::$root;
    }

    public static function baseUrl(): string
    {
        return self::$baseUrl;
    }
}
