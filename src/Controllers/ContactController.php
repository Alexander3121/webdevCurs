<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Message;

final class ContactController extends Controller
{
    public function form(): void
    {
        $user = Auth::user();
        $this->render('contacts', [
            'title'  => 'Контакты и обратная связь',
            'old'    => ['name' => $user['nickname'] ?? '', 'email' => $user['email'] ?? '', 'type' => 'question', 'text' => '', 'reply_via' => ['email']],
            'errors' => [],
        ]);
    }

    public function send(): void
    {
        $this->verifyCsrf();

        $replyVia = $_POST['reply_via'] ?? [];
        $replyVia = is_array($replyVia) ? array_values(array_intersect($replyVia, array_keys(Message::REPLY_VIA))) : [];

        $old = [
            'name'      => $this->input('name'),
            'email'     => $this->input('email'),
            'type'      => $this->input('type'),
            'text'      => $this->input('text'),
            'reply_via' => $replyVia,
        ];

        $errors = [];
        if (mb_strlen($old['name']) < 2 || mb_strlen($old['name']) > 80) {
            $errors['name'] = 'Укажите имя (от 2 до 80 символов).';
        }
        if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Введите корректный e-mail.';
        }
        if (!isset(Message::TYPES[$old['type']])) {
            $errors['type'] = 'Выберите тип обращения.';
        }
        if (mb_strlen($old['text']) < 10) {
            $errors['text'] = 'Текст обращения — минимум 10 символов.';
        }
        if (!$replyVia) {
            $errors['reply_via'] = 'Выберите хотя бы один способ ответа.';
        }

        if ($errors) {
            $this->render('contacts', ['title' => 'Контакты и обратная связь', 'old' => $old, 'errors' => $errors], 422);
            return;
        }

        $id = Message::create($old['name'], $old['email'], $old['type'], $old['text'], $replyVia);
        flash('success', 'Спасибо! Обращение №' . $id . ' принято — ответим в течение рабочего дня.');
        $this->redirect('contacts');
    }
}
