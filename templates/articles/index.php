<?php
/**
 * @var array $articles
 * @var string $q
 * @var App\Core\Paginator $pager
 */
use App\Core\Auth;
use App\Core\View;
?>
<section class="wrap section">
    <div class="section__head">
        <h1>Статьи</h1>
<?php if (Auth::check()): ?>
        <a href="<?= url('articles/new') ?>" class="btn btn--accent">+ Написать статью</a>
<?php endif; ?>
    </div>

    <form class="search" method="get" action="<?= url('articles') ?>">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Поиск по статьям: страховка, зима, скидки…">
        <button class="btn" type="submit">Найти</button>
    </form>

<?php if ($q !== ''): ?>
    <p class="found">По запросу «<?= e($q) ?>» найдено: <b><?= $pager->total ?></b> · <a href="<?= url('articles') ?>">сбросить</a></p>
<?php endif; ?>

<?php if (!$articles): ?>
    <div class="empty">Статей не найдено.</div>
<?php endif; ?>

    <div class="post-list">
<?php foreach ($articles as $a): ?>
        <article class="post post--row">
            <p class="post__meta"><?= date_ru($a['created_at']) ?> · автор: <b><?= e($a['author']) ?></b></p>
            <h2><a href="<?= url('articles/' . $a['id']) ?>"><?= e($a['title']) ?></a></h2>
            <p><?= e($a['excerpt']) ?></p>
            <a href="<?= url('articles/' . $a['id']) ?>" class="more">Читать →</a>
        </article>
<?php endforeach; ?>
    </div>

    <?= View::fetch('partials/pagination', ['pager' => $pager]) ?>
</section>
