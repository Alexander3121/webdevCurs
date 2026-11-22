<?php
/** @var array $car */
use App\Core\View;
use App\Models\Car;
?>
<article class="car-card<?= $car['is_available'] ? '' : ' is-busy' ?>">
    <a href="<?= url('cars/' . $car['id']) ?>" class="car-card__img">
        <?= View::fetch('partials/car-svg', ['color' => $car['color']]) ?>
        <span class="tag"><?= e(Car::CLASSES[$car['class']] ?? $car['class']) ?></span>
<?php if (!$car['is_available']): ?>
        <span class="tag tag--busy">Занята</span>
<?php endif; ?>
    </a>
    <div class="car-card__body">
        <h3><a href="<?= url('cars/' . $car['id']) ?>"><?= e(Car::title($car)) ?></a></h3>
        <ul class="specs">
            <li><?= (int) $car['year'] ?></li>
            <li><?= e(Car::TRANSMISSIONS[$car['transmission']] ?? '') ?></li>
            <li><?= (int) $car['power_hp'] ?> л.с.</li>
            <li><?= (int) $car['seats'] ?> мест</li>
        </ul>
        <div class="car-card__foot">
            <span class="price"><?= money($car['price_per_day']) ?><small>/сутки</small></span>
            <a class="btn btn--sm" href="<?= url('cars/' . $car['id']) ?>">Подробнее</a>
        </div>
    </div>
</article>
