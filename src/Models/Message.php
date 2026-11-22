<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Db;

/**
 * Обращения из формы обратной связи.
 */
final class Message
{
    public const TYPES = [
        'question'   => 'Вопрос',
        'complaint'  => 'Жалоба',
        'suggestion' => 'Предложение',
        'thanks'     => 'Благодарность',
    ];

    public const REPLY_VIA = [
        'sms'   => 'СМС',
        'email' => 'E-mail',
    ];

    public static function create(string $name, string $email, string $type, string $text, array $replyVia): int
    {
        Db::get()->exec(
            'INSERT INTO messages (name, email, type, text, reply_via, created_at) VALUES (?, ?, ?, ?, ?, ?)',
            [$name, $email, $type, $text, implode(',', $replyVia), date('Y-m-d H:i:s')]
        );
        return Db::get()->lastId();
    }
}
