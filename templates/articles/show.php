<?php
/**
 * @var array $article  (+ поле author — ник из таблицы users)
 * @var bool $canEdit
 */
?>
<section class="wrap section article">
    <p class="crumbs"><a href="<?= url('articles') ?>">Статьи</a> / <?= e($article['title']) ?></p>

    <h1><?= e($article['title']) ?></h1>
    <p class="post__meta">
        Автор: <b><?= e($article['author']) ?></b> · <?= date_ru($article['created_at']) ?>
<?php if ($article['updated_at']): ?>
        · изменено <?= date_ru($article['updated_at']) ?>
<?php endif; ?>
    </p>

    <p class="lead"><?= e($article['excerpt']) ?></p>

    <div class="article__body">
<?php foreach (preg_split('/\n{2,}/', trim($article['body'])) as $paragraph): ?>
        <p><?= nl2br(e($paragraph)) ?></p>
<?php endforeach; ?>
    </div>

<?php if ($canEdit): ?>
    <div class="article__tools">
        <a class="btn btn--ghost" href="<?= url('articles/' . $article['id'] . '/edit') ?>">Редактировать</a>
        <form method="post" action="<?= url('articles/' . $article['id'] . '/delete') ?>" data-confirm="Удалить статью безвозвратно?">
            <?= csrf_field() ?>
            <button class="btn btn--danger" type="submit">Удалить</button>
        </form>
    </div>
<?php endif; ?>

    <p><a href="<?= url('articles') ?>" class="more">← Все статьи</a></p>
</section>
