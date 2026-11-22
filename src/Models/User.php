<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Db;

/**
 * Пользователи сайта.
 */
final class User
{
    public static function find(int $id): ?array
    {
        return Db::get()->one('SELECT id, nickname, email, created_at FROM users WHERE id = ?', [$id]);
    }

    /** Поиск по нику или e-mail — для входа */
    public static function findForLogin(string $login): ?array
    {
        return Db::get()->one('SELECT * FROM users WHERE nickname = ? OR email = ?', [$login, mb_strtolower($login)]);
    }

    public static function create(string $nickname, string $email, string $password): int
    {
        Db::get()->exec(
            'INSERT INTO users (nickname, email, password_hash, created_at) VALUES (?, ?, ?, ?)',
            [$nickname, mb_strtolower($email), password_hash($password, PASSWORD_DEFAULT), date('Y-m-d H:i:s')]
        );
        return Db::get()->lastId();
    }

    public static function validateRegistration(string $nickname, string $email, string $password, string $repeat): array
    {
        $errors = [];

        if (!preg_match('/^[a-zA-Z0-9_]{3,40}$/', $nickname)) {
            $errors['nickname'] = 'Ник — от 3 до 40 символов: латиница, цифры и «_».';
        } elseif (Db::get()->value('SELECT COUNT(*) FROM users WHERE nickname = ?', [$nickname])) {
            $errors['nickname'] = 'Такой ник уже занят.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Введите корректный e-mail.';
        } elseif (Db::get()->value('SELECT COUNT(*) FROM users WHERE email = ?', [mb_strtolower($email)])) {
            $errors['email'] = 'Пользователь с таким e-mail уже зарегистрирован.';
        }

        if (mb_strlen($password) < 8) {
            $errors['password'] = 'Пароль — минимум 8 символов.';
        } elseif ($password !== $repeat) {
            $errors['password_repeat'] = 'Пароли не совпадают.';
        }

        return $errors;
    }
}
