<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Booking;

final class AccountController extends Controller
{
    /** Личный кабинет: мои заявки */
    public function index(): void
    {
        $this->requireLogin();

        $this->render('account', [
            'title'    => 'Личный кабинет',
            'user'     => Auth::user(),
            'bookings' => Booking::forUser(Auth::id()),
        ]);
    }

    public function cancel(int $id): void
    {
        $this->requireLogin();
        $this->verifyCsrf();

        if (Booking::cancel($id, Auth::id())) {
            flash('info', 'Заявка №' . $id . ' отменена.');
        } else {
            flash('error', 'Эту заявку нельзя отменить.');
        }
        $this->redirect('account');
    }
}
