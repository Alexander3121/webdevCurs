<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

/**
 * Авторизация через сессию: в $_SESSION хранится только id пользователя.
 */
final class Auth
{
    private static ?array $user = null;
    private static bool $loaded = false;

    public static function user(): ?array
    {
        if (!self::$loaded) {
            self::$loaded = true;
            $id = $_SESSION['user_id'] ?? null;
            self::$user = $id ? User::find((int) $id) : null;
        }
        return self::$user;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function id(): ?int
    {
        return self::user()['id'] ?? null;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true); // защита от фиксации сессии
        $_SESSION['user_id'] = (int) $user['id'];
        self::$user   = $user;
        self::$loaded = true;
    }

    public static function logout(): void
    {
        unset($_SESSION['user_id']);
        session_regenerate_id(true);
        self::$user   = null;
        self::$loaded = true;
    }
}
