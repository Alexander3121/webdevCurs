<?php
/**
 * @var int $code
 * @var string $message
 * @var string $details
 */
?>
<section class="wrap section error-page">
    <p class="error-page__code"><?= (int) $code ?></p>
    <h1><?= e($message) ?></h1>
<?php if ($details !== ''): ?>
    <p class="muted">Причина: <?= e($details) ?></p>
<?php endif; ?>
    <p><a class="btn" href="<?= url() ?>">На главную</a> <a class="btn btn--ghost" href="<?= url('cars') ?>">В каталог</a></p>
</section>
