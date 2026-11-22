<?php
/**
 * Создание и редактирование статьи.
 * @var array $article
 * @var string $action
 * @var array $errors
 * @var string $title
 */
?>
<section class="wrap section narrow">
    <h1><?= e($title) ?></h1>

    <form class="panel form" method="post" action="<?= e($action) ?>">
        <?= csrf_field() ?>

        <label class="field field--wide<?= isset($errors['title']) ? ' has-error' : '' ?>">
            <span>Заголовок</span>
            <input type="text" name="title" value="<?= e($article['title']) ?>" maxlength="200" required>
            <?php if (isset($errors['title'])): ?><em><?= e($errors['title']) ?></em><?php endif; ?>
        </label>

        <label class="field field--wide<?= isset($errors['excerpt']) ? ' has-error' : '' ?>">
            <span>Краткое описание (показывается в списке)</span>
            <textarea name="excerpt" rows="2" maxlength="400" required><?= e($article['excerpt']) ?></textarea>
            <?php if (isset($errors['excerpt'])): ?><em><?= e($errors['excerpt']) ?></em><?php endif; ?>
        </label>

        <label class="field field--wide<?= isset($errors['body']) ? ' has-error' : '' ?>">
            <span>Текст статьи (абзацы разделяйте пустой строкой)</span>
            <textarea name="body" rows="12" required><?= e($article['body']) ?></textarea>
            <?php if (isset($errors['body'])): ?><em><?= e($errors['body']) ?></em><?php endif; ?>
        </label>

        <div class="form__actions">
            <button class="btn btn--accent" type="submit">Сохранить</button>
            <a href="<?= isset($article['id']) ? url('articles/' . $article['id']) : url('articles') ?>" class="link-reset">Отмена</a>
        </div>
    </form>
</section>
