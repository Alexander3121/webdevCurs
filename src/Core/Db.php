<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Обёртка над PDO. Одно подключение на запрос (паттерн Singleton).
 */
final class Db
{
    private static ?Db $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $dsn = getenv('APP_DB_DSN') ?: sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            App::config('db.host'),
            App::config('db.name')
        );

        $this->pdo = new PDO($dsn, App::config('db.user'), App::config('db.password'), [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }

    public static function get(): self
    {
        return self::$instance ??= new self();
    }

    /** Все строки результата */
    public function all(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Одна строка или null */
    public function one(string $sql, array $params = []): ?array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /** Одно значение (COUNT и т.п.) */
    public function value(string $sql, array $params = []): mixed
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    /** INSERT / UPDATE / DELETE — возвращает число затронутых строк */
    public function exec(string $sql, array $params = []): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function lastId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }
}
