<?php
/**
 * Поля расчёта: даты, водитель, опции. Общие для калькулятора и бронирования.
 * @var array $form
 * @var array $options
 */
$today = date('Y-m-d');
?>
<div class="fields">
    <label class="field">
        <span>Дата получения</span>
        <input type="date" name="from" value="<?= e($form['from']) ?>" min="<?= $today ?>" required data-from>
    </label>
    <label class="field">
        <span>Дата возврата</span>
        <input type="date" name="to" value="<?= e($form['to']) ?>" min="<?= $today ?>" required data-to>
    </label>
    <label class="field">
        <span>Возраст водителя</span>
        <input type="number" name="age" value="<?= (int) $form['age'] ?>" min="18" max="80" required>
    </label>
    <label class="field">
        <span>Стаж, лет</span>
        <input type="number" name="exp" value="<?= (int) $form['exp'] ?>" min="0" max="62" required>
    </label>
</div>

<fieldset class="opts">
    <legend>Дополнительно</legend>
<?php foreach ($options as $code => $opt): ?>
    <label class="opt">
        <input type="checkbox" name="options[]" value="<?= e($code) ?>"<?= in_array($code, $form['options'], true) ? ' checked' : '' ?>>
        <span class="opt__box">
            <b><?= e($opt['label']) ?></b>
            <small><?php
                if (isset($opt['per_day']))      { echo money($opt['per_day']) . ' / сутки'; }
                elseif (isset($opt['percent']))  { echo '+' . $opt['percent'] . '% к аренде'; }
                else                             { echo money($opt['fixed']) . ' разово'; }
            ?></small>
        </span>
    </label>
<?php endforeach; ?>
</fieldset>
