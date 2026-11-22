<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Рендер шаблонов: сначала шаблон страницы, затем общий layout,
 * в который страница подставляется переменной $content.
 */
final class View
{
    public const DEFAULT_TITLE = 'АвтоПрокат — аренда автомобилей';

    public static function render(string $template, array $vars = [], int $code = 200): void
    {
        http_response_code($code);

        $content = self::fetch($template, $vars);

        // В layout доступны все переменные страницы, в том числе $title (если передан)
        echo self::fetch('layout', $vars + ['content' => $content]);
    }

    /** Отрисовать шаблон в строку */
    public static function fetch(string $template, array $vars = []): string
    {
        $file = App::root() . '/templates/' . $template . '.php';

        extract($vars, EXTR_SKIP);
        ob_start();
        include $file;
        return (string) ob_get_clean();
    }

    /** Отдать JSON (для калькулятора) */
    public static function json(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public static function error(int $code, string $message, string $details = ''): void
    {
        self::render('errors/error', [
            'title'   => $code . ' — ' . $message,
            'code'    => $code,
            'message' => $message,
            'details' => $details,
        ], $code);
    }
}
