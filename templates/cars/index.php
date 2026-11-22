<?php
/**
 * @var array $cars
 * @var array $filters
 * @var App\Core\Paginator $pager
 */
use App\Core\View;
use App\Models\Car;
?>
<section class="wrap section">
    <h1>Каталог автомобилей</h1>

    <form class="filters" method="get" action="<?= url('cars') ?>">
        <input type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="Марка или модель, например «Toyota»" class="filters__search">

        <div class="chips">
            <label class="chip"><input type="radio" name="class" value=""<?= $filters['class'] === '' ? ' checked' : '' ?>><span>Все классы</span></label>
<?php foreach (Car::CLASSES as $key => $label): ?>
            <label class="chip"><input type="radio" name="class" value="<?= $key ?>"<?= $filters['class'] === $key ? ' checked' : '' ?>><span><?= $label ?></span></label>
<?php endforeach; ?>
        </div>

        <div class="filters__row">
            <select name="transmission">
                <option value="">Любая коробка</option>
<?php foreach (Car::TRANSMISSIONS as $key => $label): ?>
                <option value="<?= $key ?>"<?= $filters['transmission'] === $key ? ' selected' : '' ?>><?= $label ?></option>
<?php endforeach; ?>
            </select>
            <select name="sort">
<?php foreach (Car::SORTS as $key => $s): ?>
                <option value="<?= $key ?>"<?= $filters['sort'] === $key ? ' selected' : '' ?>><?= $s['label'] ?></option>
<?php endforeach; ?>
            </select>
            <button class="btn" type="submit">Показать</button>
            <a class="link-reset" href="<?= url('cars') ?>">Сбросить</a>
        </div>
    </form>

    <p class="found">Найдено: <b><?= $pager->total ?></b> <?= plural($pager->total, 'автомобиль', 'автомобиля', 'автомобилей') ?></p>

<?php if (!$cars): ?>
    <div class="empty">По вашему запросу ничего не нашлось. Попробуйте изменить фильтры.</div>
<?php else: ?>
    <div class="cards">
<?php foreach ($cars as $car): ?>
        <?= View::fetch('partials/car-card', ['car' => $car]) ?>
<?php endforeach; ?>
    </div>
<?php endif; ?>

    <?= View::fetch('partials/pagination', ['pager' => $pager]) ?>
</section>
