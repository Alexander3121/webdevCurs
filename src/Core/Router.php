<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Роутер: сопоставляет метод + адрес с регулярным выражением
 * и вызывает нужный экшн контроллера, передавая ему параметры из адреса.
 */
final class Router
{
    /** @param array<int, array{0:string,1:string,2:array{0:class-string,1:string}}> $routes */
    public function __construct(private array $routes)
    {
    }

    public function dispatch(string $method, string $path): void
    {
        $pathExists = false;

        foreach ($this->routes as [$routeMethod, $pattern, $handler]) {
            if (!preg_match('~^' . $pattern . '$~u', $path, $matches)) {
                continue;
            }

            $pathExists = true;
            if ($routeMethod !== $method) {
                continue;
            }

            array_shift($matches);
            [$controllerClass, $action] = $handler;

            // Параметры из URL приходят строками; числовые приводим к int
            $params = array_map(fn ($v) => ctype_digit($v) ? (int) $v : $v, $matches);

            try {
                (new $controllerClass())->$action(...$params);
            } catch (\PDOException $e) {
                View::error(500, 'Ошибка базы данных', $e->getMessage());
            }
            return;
        }

        if ($pathExists) {
            View::error(405, 'Метод не поддерживается');
            return;
        }

        View::error(404, 'Страница не найдена');
    }
}
