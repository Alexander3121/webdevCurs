<?php
/**
 * Разбивка стоимости. Используется на странице и в ответе API для JavaScript.
 * @var array $result
 */
use App\Services\PriceCalculator;

$d = $result['days'];
?>
<dl class="bill">
    <div><dt>Аренда: <?= money($result['price_per_day']) ?> × <?= $d ?> <?= plural($d, 'сутки', 'суток', 'суток') ?></dt><dd><?= money($result['base']) ?></dd></div>
<?php if ($result['discount']): ?>
    <div class="bill__minus"><dt>Скидка за длительную аренду −<?= $result['discount_percent'] ?>%</dt><dd>−<?= money($result['discount']) ?></dd></div>
<?php endif; ?>
<?php if ($result['young']): ?>
    <div class="bill__plus"><dt>Надбавка молодому водителю +<?= PriceCalculator::YOUNG_SURCHARGE ?>%</dt><dd>+<?= money($result['surcharge']) ?></dd></div>
<?php endif; ?>
<?php foreach ($result['options'] as $opt): ?>
    <div><dt><?= e($opt['label']) ?> <small>(<?= e($opt['note']) ?>)</small></dt><dd><?= money($opt['sum']) ?></dd></div>
<?php endforeach; ?>
    <div class="bill__total"><dt>Итого к оплате</dt><dd><?= money($result['total']) ?></dd></div>
</dl>
<p class="bill__note">
    В среднем <?= money($result['avg_per_day']) ?> в сутки.
    Залог <?= money($result['deposit']) ?> блокируется на карте и возвращается после сдачи машины.
</p>
