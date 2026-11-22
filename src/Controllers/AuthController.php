<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\User;

final class AuthController extends Controller
{
    public function registerForm(): void
    {
        if (Auth::check()) {
            $this->redirect('account');
        }
        $this->render('auth/register', ['title' => 'Регистрация', 'old' => [], 'errors' => []]);
    }

    public function register(): void
    {
        $this->verifyCsrf();

        $nickname = $this->input('nickname');
        $email    = $this->input('email');
        $password = (string) ($_POST['password'] ?? '');
        $repeat   = (string) ($_POST['password_repeat'] ?? '');

        $errors = User::validateRegistration($nickname, $email, $password, $repeat);
        if ($errors) {
            $this->render('auth/register', [
                'title'  => 'Регистрация',
                'old'    => ['nickname' => $nickname, 'email' => $email],
                'errors' => $errors,
            ], 422);
            return;
        }

        $id = User::create($nickname, $email, $password);
        Auth::login(User::find($id));

        flash('success', 'Добро пожаловать, ' . $nickname . '! Аккаунт создан.');
        $this->redirect('account');
    }

    public function loginForm(): void
    {
        if (Auth::check()) {
            $this->redirect('account');
        }
        // ?back=cars/5 — куда вернуть после входа (только внутренние адреса)
        $back = $_GET['back'] ?? '';
        if (is_string($back) && preg_match('~^[a-z0-9/_-]{1,100}$~', $back)) {
            $_SESSION['after_login'] = $back;
        }
        $this->render('auth/login', ['title' => 'Вход', 'login' => '', 'error' => '']);
    }

    public function login(): void
    {
        $this->verifyCsrf();

        $login    = $this->input('login');
        $password = (string) ($_POST['password'] ?? '');
        $user     = $login !== '' ? User::findForLogin($login) : null;

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->render('auth/login', [
                'title' => 'Вход',
                'login' => $login,
                'error' => 'Неверный логин или пароль.',
            ], 422);
            return;
        }

        Auth::login($user);
        flash('success', 'Вы вошли как ' . $user['nickname'] . '.');

        // Вернуть туда, куда пользователь шёл до входа
        $back = $_SESSION['after_login'] ?? 'account';
        unset($_SESSION['after_login']);
        $this->redirect(is_string($back) && $back !== '' ? $back : 'account');
    }

    public function logout(): void
    {
        $this->verifyCsrf();
        Auth::logout();
        flash('info', 'Вы вышли из аккаунта.');
        $this->redirect('');
    }
}
