<?php
/**
 * @var array $user
 * @var array $bookings
 */
use App\Models\Booking;
use App\Services\PriceCalculator;
?>
<section class="wrap section">
    <div class="section__head">
        <div>
            <h1>Личный кабинет</h1>
            <p class="muted"><?= e($user['nickname']) ?> · <?= e($user['email']) ?> · с нами с <?= date_ru($user['created_at']) ?></p>
        </div>
        <div class="row-gap">
            <a href="<?= url('cars') ?>" class="btn btn--accent">Забронировать машину</a>
            <a href="<?= url('articles/new') ?>" class="btn btn--ghost">Написать статью</a>
        </div>
    </div>

    <h2>Мои заявки</h2>
<?php if (!$bookings): ?>
    <div class="empty">Заявок пока нет. Выберите машину в <a href="<?= url('cars') ?>">каталоге</a>.</div>
<?php else: ?>
    <div class="table-scroll">
    <table class="table">
        <thead>
            <tr><th>№</th><th>Автомобиль</th><th>Даты</th><th>Опции</th><th>Сумма</th><th>Статус</th><th></th></tr>
        </thead>
        <tbody>
<?php foreach ($bookings as $b): ?>
            <tr>
                <td><?= (int) $b['id'] ?></td>
                <td><span class="dot" style="background: <?= e($b['color']) ?>"></span><a href="<?= url('cars/' . $b['car_id']) ?>"><?= e($b['brand'] . ' ' . $b['model']) ?></a></td>
                <td><?= date_ru($b['date_from']) ?> — <?= date_ru($b['date_to']) ?><br><small class="muted"><?= (int) $b['days'] ?> <?= plural((int) $b['days'], 'сутки', 'суток', 'суток') ?></small></td>
                <td><small><?php
                    $labels = [];
                    foreach (array_filter(explode(',', $b['options'])) as $code) {
                        $labels[] = PriceCalculator::OPTIONS[$code]['label'] ?? $code;
                    }
                    echo $labels ? e(implode(', ', $labels)) : '—';
                ?></small></td>
                <td><b><?= money($b['total']) ?></b></td>
                <td><span class="status status--<?= e($b['status']) ?>"><?= e(Booking::STATUSES[$b['status']] ?? $b['status']) ?></span></td>
                <td>
<?php if ($b['status'] !== 'cancelled'): ?>
                    <form method="post" action="<?= url('account/bookings/' . $b['id'] . '/cancel') ?>" data-confirm="Отменить заявку №<?= (int) $b['id'] ?>?">
                        <?= csrf_field() ?>
                        <button class="link-danger" type="submit">Отменить</button>
                    </form>
<?php endif; ?>
                </td>
            </tr>
<?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php endif; ?>
</section>
