<?php
/**
 * Небольшие функции-помощники для шаблонов и контроллеров.
 */

declare(strict_types=1);

use App\Core\App;

/** Экранирование вывода */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Ссылка внутри сайта: url('cars/5') -> /autoprokat/cars/5 */
function url(string $path = '', array $query = []): string
{
    $link = App::baseUrl() . '/' . ltrim($path, '/');
    $query = array_filter($query, fn ($v) => $v !== '' && $v !== null);
    return $query ? $link . '?' . http_build_query($query) : $link;
}

/** Путь к статике с версией файла, чтобы браузер не кешировал старый CSS */
function asset(string $path): string
{
    $file = App::root() . '/assets/' . $path;
    $v = is_file($file) ? filemtime($file) : 1;
    return App::baseUrl() . '/assets/' . $path . '?v=' . $v;
}

/** Цена: 12500 -> "12 500 ₽" */
function money(int|float $value): string
{
    return number_format((float) $value, 0, ',', ' ') . ' ₽';
}

/** Дата из БД -> 22.11.2026 */
function date_ru(?string $date): string
{
    return $date ? date('d.m.Y', strtotime($date)) : '';
}

/** Склонение: plural(3, 'день', 'дня', 'дней') */
function plural(int $n, string $one, string $few, string $many): string
{
    $n10 = $n % 10;
    $n100 = $n % 100;
    if ($n10 === 1 && $n100 !== 11) {
        return $one;
    }
    if ($n10 >= 2 && $n10 <= 4 && ($n100 < 10 || $n100 >= 20)) {
        return $few;
    }
    return $many;
}

/* ---------- CSRF ---------- */

function csrf_token(): string
{
    return $_SESSION['_token'] ??= bin2hex(random_bytes(32));
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
}

/* ---------- Flash-сообщения (живут до следующего показа) ---------- */

function flash(string $type, string $text): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'text' => $text];
}

function take_flashes(): array
{
    $list = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $list;
}
