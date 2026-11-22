<?php
/**
 * Маршруты: [HTTP-метод, регулярное выражение адреса, [контроллер, экшн]].
 * Группы в скобках передаются в экшн параметрами.
 */

use App\Controllers\AccountController;
use App\Controllers\ArticlesController;
use App\Controllers\AuthController;
use App\Controllers\BookingController;
use App\Controllers\CalculatorController;
use App\Controllers\CarsController;
use App\Controllers\ContactController;
use App\Controllers\HomeController;

return [
    ['GET',  '',                          [HomeController::class, 'index']],

    // Каталог и бронирование
    ['GET',  'cars',                      [CarsController::class, 'index']],
    ['GET',  'cars/(\d+)',                [CarsController::class, 'show']],
    ['POST', 'cars/(\d+)/book',           [BookingController::class, 'store']],

    // Калькулятор: страница и JSON для «живого» пересчёта
    ['GET',  'calculator',                [CalculatorController::class, 'index']],
    ['GET',  'api/calculate',             [CalculatorController::class, 'api']],

    // Статьи
    ['GET',  'articles',                  [ArticlesController::class, 'index']],
    ['GET',  'articles/new',              [ArticlesController::class, 'create']],
    ['POST', 'articles/new',              [ArticlesController::class, 'store']],
    ['GET',  'articles/(\d+)',            [ArticlesController::class, 'show']],
    ['GET',  'articles/(\d+)/edit',       [ArticlesController::class, 'edit']],
    ['POST', 'articles/(\d+)/edit',       [ArticlesController::class, 'update']],
    ['POST', 'articles/(\d+)/delete',     [ArticlesController::class, 'delete']],

    // Пользователи
    ['GET',  'register',                  [AuthController::class, 'registerForm']],
    ['POST', 'register',                  [AuthController::class, 'register']],
    ['GET',  'login',                     [AuthController::class, 'loginForm']],
    ['POST', 'login',                     [AuthController::class, 'login']],
    ['POST', 'logout',                    [AuthController::class, 'logout']],
    ['GET',  'account',                   [AccountController::class, 'index']],
    ['POST', 'account/bookings/(\d+)/cancel', [AccountController::class, 'cancel']],

    // Обратная связь
    ['GET',  'contacts',                  [ContactController::class, 'form']],
    ['POST', 'contacts',                  [ContactController::class, 'send']],
];
