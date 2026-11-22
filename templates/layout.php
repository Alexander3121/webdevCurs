<?php
/**
 * Общий каркас всех страниц.
 * @var string $content  HTML страницы
 * @var string|null $title  заголовок, переданный контроллером (может отсутствовать)
 */

use App\Core\App;
use App\Core\Auth;
use App\Core\View;

// title не передан -> заголовок по умолчанию
$pageTitle = isset($title) ? $title . ' — ' . App::config('app_name') : View::DEFAULT_TITLE;

$current = (string) ($_GET['route'] ?? '');
$nav = [
    'cars'       => 'Каталог',
    'calculator' => 'Калькулятор',
    'articles'   => 'Статьи',
    'contacts'   => 'Контакты',
];
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <script src="<?= asset('js/app.js') ?>" defer></script>
</head>
<body>

<header class="header">
    <div class="wrap header__row">
        <a class="logo" href="<?= url() ?>">
            <svg class="logo__icon" viewBox="0 0 32 32" aria-hidden="true"><path d="M5 19l2.5-7A3 3 0 0 1 10.3 10h11.4a3 3 0 0 1 2.8 2l2.5 7v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-1H9v1a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1z" fill="currentColor"/><circle cx="10" cy="20" r="1.8" fill="#16181d"/><circle cx="22" cy="20" r="1.8" fill="#16181d"/><path d="M9 17l1.6-4.4a1 1 0 0 1 .9-.6h9a1 1 0 0 1 .9.6L23 17z" fill="#16181d"/></svg>
            <span>Авто<b>Прокат</b></span>
        </a>

        <button class="burger" type="button" aria-label="Меню" data-burger><span></span></button>

        <nav class="nav" data-nav>
<?php foreach ($nav as $path => $label): ?>
            <a href="<?= url($path) ?>" class="nav__link<?= str_starts_with($current, $path) ? ' is-active' : '' ?>"><?= $label ?></a>
<?php endforeach; ?>
            <span class="nav__sep"></span>
<?php if ($user): ?>
            <a href="<?= url('account') ?>" class="nav__link nav__user<?= $current === 'account' ? ' is-active' : '' ?>">👤 <?= e($user['nickname']) ?></a>
            <form method="post" action="<?= url('logout') ?>" class="nav__logout">
                <?= csrf_field() ?>
                <button type="submit" class="nav__link">Выйти</button>
            </form>
<?php else: ?>
            <a href="<?= url('login') ?>" class="nav__link<?= $current === 'login' ? ' is-active' : '' ?>">Вход</a>
            <a href="<?= url('register') ?>" class="btn btn--accent btn--sm">Регистрация</a>
<?php endif; ?>
        </nav>
    </div>
</header>

<main class="main">
    <div class="wrap">
<?php foreach (take_flashes() as $f): ?>
        <div class="flash flash--<?= e($f['type']) ?>"><?= e($f['text']) ?></div>
<?php endforeach; ?>
    </div>

<?= $content ?>
</main>

<footer class="footer">
    <div class="wrap footer__row">
        <div>
            <div class="logo logo--footer"><span>Авто<b>Прокат</b></span></div>
            <p>Аренда автомобилей в Москве без скрытых платежей.<br>Ежедневно с 8:00 до 22:00 · +7 (495) 000-00-00</p>
        </div>
        <div class="footer__links">
<?php foreach ($nav as $path => $label): ?>
            <a href="<?= url($path) ?>"><?= $label ?></a>
<?php endforeach; ?>
        </div>
        <div class="footer__meta">
            <p>Курсовой проект по дисциплине «Веб-программирование»</p>
            <p>Выполнил: <?= e(App::config('author')) ?></p>
            <p>Московский Политех, <?= date('Y') ?></p>
        </div>
    </div>
</footer>

</body>
</html>
