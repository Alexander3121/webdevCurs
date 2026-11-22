<?php
/**
 * @var array $cars
 * @var array $options
 * @var array $form
 * @var array|null $result
 * @var array $errors
 * @var array|null $car
 */
use App\Core\View;
use App\Models\Car;
use App\Services\PriceCalculator;
?>
<section class="wrap section">
    <h1>Калькулятор стоимости аренды</h1>
    <p class="lead">Выберите машину и параметры — сумма пересчитывается сразу. Кнопка «Рассчитать» работает и без JavaScript.</p>

    <div class="calc">
        <form class="panel" method="get" action="<?= url('calculator') ?>" data-calc="<?= url('api/calculate') ?>">
            <label class="field field--wide">
                <span>Автомобиль</span>
                <select name="car" required>
                    <option value="">— выберите —</option>
<?php foreach ($cars as $c): ?>
                    <option value="<?= (int) $c['id'] ?>"<?= (int) $form['car'] === (int) $c['id'] ? ' selected' : '' ?>>
                        <?= e(Car::title($c)) ?> · <?= e(Car::CLASSES[$c['class']] ?? '') ?> · <?= money($c['price_per_day']) ?>/сут
                    </option>
<?php endforeach; ?>
                </select>
            </label>

            <?= View::fetch('partials/calc-fields', ['form' => $form, 'options' => $options]) ?>

            <button type="submit" class="btn">Рассчитать</button>
        </form>

        <div class="panel calc__result">
            <h2>Стоимость</h2>
            <div class="price-box" data-result>
<?php foreach ($errors as $err): ?>
                <div class="flash flash--error"><?= e($err) ?></div>
<?php endforeach; ?>
<?php if ($result): ?>
                <?= View::fetch('partials/price', ['result' => $result]) ?>
<?php elseif (!$errors): ?>
                <p class="muted">Выберите автомобиль, чтобы увидеть расчёт.</p>
<?php endif; ?>
            </div>
            <a class="btn btn--accent btn--block" data-book-link href="<?= url('cars/' . (int) $form['car']) ?>"<?= $result ? '' : ' hidden' ?>>Перейти к бронированию</a>

            <div class="rules">
                <h3>Как считается цена</h3>
                <ul>
<?php foreach (array_reverse(PriceCalculator::DISCOUNTS, true) as $days => $percent): ?>
                    <li>от <?= $days ?> суток — скидка <?= $percent ?>%</li>
<?php endforeach; ?>
                    <li>водителю младше <?= PriceCalculator::YOUNG_AGE ?> лет или со стажем меньше <?= PriceCalculator::YOUNG_EXPERIENCE ?> лет — надбавка <?= PriceCalculator::YOUNG_SURCHARGE ?>%</li>
                    <li>страховка считается от стоимости аренды после скидки</li>
                </ul>
            </div>
        </div>
    </div>
</section>
