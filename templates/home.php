<?php
/**
 * @var array $cars
 * @var array $articles
 */
use App\Core\View;
use App\Models\Car;
?>
<section class="hero">
    <div class="wrap hero__row">
        <div class="hero__text">
            <p class="eyebrow">Аренда авто в Москве</p>
            <h1>Машина на день, неделю или месяц — <span class="mark">за 15 минут</span></h1>
            <p class="lead">14 автомобилей от эконома до минивэна. Цена считается честно и сразу: без звонков и скрытых платежей.</p>
            <div class="hero__actions">
                <a href="<?= url('cars') ?>" class="btn btn--accent">Выбрать машину</a>
                <a href="<?= url('calculator') ?>" class="btn btn--ghost">Рассчитать стоимость</a>
            </div>
            <ul class="hero__facts">
                <li><b>от 1 900 ₽</b> в сутки</li>
                <li><b>−25%</b> при аренде на месяц</li>
                <li><b>24/7</b> помощь на дороге</li>
            </ul>
        </div>
        <div class="hero__pic">
            <?= View::fetch('partials/car-svg', ['color' => '#ffc533']) ?>
            <div class="road"></div>
        </div>
    </div>
</section>

<section class="wrap section">
    <div class="section__head">
        <h2>Популярные модели</h2>
        <a href="<?= url('cars') ?>" class="more">Весь каталог →</a>
    </div>
    <div class="cards">
<?php foreach ($cars as $car): ?>
        <?= View::fetch('partials/car-card', ['car' => $car]) ?>
<?php endforeach; ?>
    </div>
</section>

<section class="wrap section">
    <h2>Как это работает</h2>
    <ol class="steps">
        <li><b>Выберите машину</b>Фильтруйте каталог по классу и коробке передач.</li>
        <li><b>Посчитайте цену</b>Калькулятор учтёт срок, опции, страховку и стаж.</li>
        <li><b>Оставьте заявку</b>Менеджер перезвонит и подтвердит бронь.</li>
        <li><b>Заберите ключи</b>В офисе или с подачей по вашему адресу.</li>
    </ol>
</section>

<section class="wrap section">
    <div class="section__head">
        <h2>Свежие статьи</h2>
        <a href="<?= url('articles') ?>" class="more">Все статьи →</a>
    </div>
    <div class="posts">
<?php foreach ($articles as $a): ?>
        <article class="post">
            <p class="post__meta"><?= date_ru($a['created_at']) ?> · <?= e($a['author']) ?></p>
            <h3><a href="<?= url('articles/' . $a['id']) ?>"><?= e($a['title']) ?></a></h3>
            <p><?= e($a['excerpt']) ?></p>
        </article>
<?php endforeach; ?>
    </div>
</section>
