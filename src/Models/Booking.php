<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Db;

/**
 * Заявки на аренду.
 */
final class Booking
{
    public const STATUSES = [
        'new'       => 'Новая',
        'confirmed' => 'Подтверждена',
        'cancelled' => 'Отменена',
    ];

    public static function create(array $data): int
    {
        Db::get()->exec(
            'INSERT INTO bookings (user_id, car_id, date_from, date_to, days, options, driver_age, experience, total, status, comment, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $data['user_id'], $data['car_id'], $data['date_from'], $data['date_to'], $data['days'],
                $data['options'], $data['driver_age'], $data['experience'], $data['total'],
                'new', $data['comment'], date('Y-m-d H:i:s'),
            ]
        );
        return Db::get()->lastId();
    }

    /** Заявки пользователя вместе с названием машины */
    public static function forUser(int $userId): array
    {
        return Db::get()->all(
            'SELECT b.*, c.brand, c.model, c.color
             FROM bookings b JOIN cars c ON c.id = b.car_id
             WHERE b.user_id = ?
             ORDER BY b.created_at DESC, b.id DESC',
            [$userId]
        );
    }

    /** Отменить можно только свою и ещё не отменённую заявку */
    public static function cancel(int $id, int $userId): bool
    {
        return Db::get()->exec(
            "UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status <> 'cancelled'",
            [$id, $userId]
        ) > 0;
    }
}
