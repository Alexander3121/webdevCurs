<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Db;

/**
 * Статьи блога. Автор статьи хранится в users (связь по author_id).
 */
final class Article
{
    /** Статья вместе с ником автора */
    public static function find(int $id): ?array
    {
        return Db::get()->one(
            'SELECT a.*, u.nickname AS author
             FROM articles a JOIN users u ON u.id = a.author_id
             WHERE a.id = ?',
            [$id]
        );
    }

    public static function latest(int $limit): array
    {
        return Db::get()->all(
            'SELECT a.id, a.title, a.excerpt, a.created_at, u.nickname AS author
             FROM articles a JOIN users u ON u.id = a.author_id
             ORDER BY a.created_at DESC, a.id DESC LIMIT ' . $limit
        );
    }

    /**
     * Поиск по заголовку и тексту + пагинация
     * @return array{items:array,total:int}
     */
    public static function search(string $query, int $page, int $perPage): array
    {
        $where  = '';
        $params = [];
        if ($query !== '') {
            $where  = 'WHERE a.title LIKE ? OR a.excerpt LIKE ? OR a.body LIKE ?';
            $like   = '%' . $query . '%';
            $params = [$like, $like, $like];
        }

        $total  = (int) Db::get()->value("SELECT COUNT(*) FROM articles a $where", $params);
        $offset = max(0, ($page - 1) * $perPage);

        $items = Db::get()->all(
            "SELECT a.id, a.title, a.excerpt, a.created_at, u.nickname AS author
             FROM articles a JOIN users u ON u.id = a.author_id
             $where
             ORDER BY a.created_at DESC, a.id DESC
             LIMIT $perPage OFFSET $offset",
            $params
        );

        return ['items' => $items, 'total' => $total];
    }

    public static function create(int $authorId, string $title, string $excerpt, string $body): int
    {
        Db::get()->exec(
            'INSERT INTO articles (author_id, title, excerpt, body, created_at) VALUES (?, ?, ?, ?, ?)',
            [$authorId, $title, $excerpt, $body, date('Y-m-d H:i:s')]
        );
        return Db::get()->lastId();
    }

    public static function update(int $id, string $title, string $excerpt, string $body): void
    {
        Db::get()->exec(
            'UPDATE articles SET title = ?, excerpt = ?, body = ?, updated_at = ? WHERE id = ?',
            [$title, $excerpt, $body, date('Y-m-d H:i:s'), $id]
        );
    }

    public static function delete(int $id): void
    {
        Db::get()->exec('DELETE FROM articles WHERE id = ?', [$id]);
    }

    /** Проверка полей формы статьи. Возвращает список ошибок */
    public static function validate(string $title, string $excerpt, string $body): array
    {
        $errors = [];
        if (mb_strlen($title) < 5 || mb_strlen($title) > 200) {
            $errors['title'] = 'Заголовок — от 5 до 200 символов.';
        }
        if (mb_strlen($excerpt) < 10 || mb_strlen($excerpt) > 400) {
            $errors['excerpt'] = 'Краткое описание — от 10 до 400 символов.';
        }
        if (mb_strlen($body) < 30) {
            $errors['body'] = 'Текст статьи — минимум 30 символов.';
        }
        return $errors;
    }
}
