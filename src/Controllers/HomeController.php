<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Article;
use App\Models\Car;

final class HomeController extends Controller
{
    public function index(): void
    {
        // title не передаём — в шаблоне сработает заголовок по умолчанию
        $this->render('home', [
            'cars'     => Car::popular(3),
            'articles' => Article::latest(3),
        ]);
    }
}
