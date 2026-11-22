<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Базовый контроллер: общие методы для всех контроллеров.
 */
abstract class Controller
{
    protected function render(string $template, array $vars = [], int $code = 200): void
    {
        View::render($template, $vars, $code);
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function notFound(string $message = 'Страница не найдена'): void
    {
        View::error(404, $message);
    }

    /** Пускать дальше только авторизованных, остальных — на вход */
    protected function requireLogin(): void
    {
        if (!Auth::check()) {
            flash('info', 'Войдите, чтобы продолжить.');
            // Вернуться после входа можно только на GET-страницу
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
                $_SESSION['after_login'] = (string) ($_GET['route'] ?? '');
            }
            $this->redirect('login');
        }
    }

    /** Проверка CSRF-токена у POST-запроса */
    protected function verifyCsrf(): void
    {
        $token = $_POST['_token'] ?? '';
        if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
            View::error(419, 'Сессия устарела, обновите страницу и повторите');
            exit;
        }
    }

    /** Строка из POST без пробелов по краям */
    protected function input(string $key, string $default = ''): string
    {
        $value = $_POST[$key] ?? $default;
        return is_string($value) ? trim($value) : $default;
    }
}
