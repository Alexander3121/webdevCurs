<?php
/**
 * @var array $car
 * @var array $options
 * @var array $form
 * @var array $errors
 */
use App\Core\Auth;
use App\Core\View;
use App\Models\Car;
?>
<section class="wrap section">
    <p class="crumbs"><a href="<?= url('cars') ?>">Каталог</a> / <?= e(Car::title($car)) ?></p>

    <div class="car-page">
        <div class="car-page__info">
            <div class="car-page__img"><?= View::fetch('partials/car-svg', ['color' => $car['color']]) ?></div>
            <h1><?= e(Car::title($car)) ?> <span class="tag"><?= e(Car::CLASSES[$car['class']] ?? '') ?></span></h1>
            <p class="lead"><?= e($car['description']) ?></p>

            <table class="spec-table">
                <tr><th>Год выпуска</th><td><?= (int) $car['year'] ?></td></tr>
                <tr><th>Коробка</th><td><?= e(Car::TRANSMISSIONS[$car['transmission']] ?? '') ?></td></tr>
                <tr><th>Топливо</th><td><?= e($car['fuel']) ?></td></tr>
                <tr><th>Мощность</th><td><?= (int) $car['power_hp'] ?> л.с.</td></tr>
                <tr><th>Мест</th><td><?= (int) $car['seats'] ?></td></tr>
                <tr><th>Цена</th><td><b><?= money($car['price_per_day']) ?></b> / сутки</td></tr>
                <tr><th>Залог</th><td><?= money($car['deposit']) ?></td></tr>
            </table>
        </div>

        <aside class="panel car-page__book">
            <h2>Бронирование</h2>
<?php if (!$car['is_available']): ?>
            <div class="flash flash--error">Машина сейчас в аренде. Посмотрите другие модели этого класса.</div>
            <a class="btn" href="<?= url('cars', ['class' => $car['class']]) ?>">Похожие машины</a>
<?php else: ?>
<?php foreach ($errors as $err): ?>
            <div class="flash flash--error"><?= e($err) ?></div>
<?php endforeach; ?>
            <form method="post" action="<?= url('cars/' . $car['id'] . '/book') ?>" data-calc="<?= url('api/calculate') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="car" value="<?= (int) $car['id'] ?>">
                <?= View::fetch('partials/calc-fields', ['form' => $form, 'options' => $options]) ?>

                <div class="price-box" data-result>
                    <p class="muted">Укажите параметры — стоимость посчитается автоматически.</p>
                </div>

<?php if (Auth::check()): ?>
                <label class="field field--wide">
                    <span>Комментарий к заявке</span>
                    <textarea name="comment" rows="2" maxlength="500" placeholder="Например: нужна подача к метро"><?= e($form['comment']) ?></textarea>
                </label>
                <button type="submit" class="btn btn--accent btn--block">Забронировать</button>
<?php else: ?>
                <p class="muted">Чтобы оформить заявку, <a href="<?= url('login', ['back' => 'cars/' . $car['id']]) ?>">войдите</a> или <a href="<?= url('register') ?>">зарегистрируйтесь</a>.</p>
<?php endif; ?>
            </form>
<?php endif; ?>
        </aside>
    </div>
</section>
