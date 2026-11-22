<?php
/**
 * @var array $old
 * @var array $errors
 */
use App\Models\Message;
?>
<section class="wrap section">
    <h1>Контакты</h1>

    <div class="contacts">
        <div class="contacts__info">
            <div class="panel">
                <h3>Офис выдачи</h3>
                <p>Москва, ул. Большая Семёновская, 38<br>5 минут пешком от м. «Электрозаводская»</p>
                <h3>Телефон</h3>
                <p><a href="tel:+74950000000">+7 (495) 000-00-00</a><br>ежедневно с 8:00 до 22:00</p>
                <h3>Помощь на дороге</h3>
                <p>круглосуточно: <a href="tel:+74950000001">+7 (495) 000-00-01</a></p>
            </div>
        </div>

        <form class="panel form" method="post" action="<?= url('contacts') ?>">
            <h2>Обратная связь</h2>
            <?= csrf_field() ?>

            <div class="fields">
                <label class="field<?= isset($errors['name']) ? ' has-error' : '' ?>">
                    <span>Ваше имя</span>
                    <input type="text" name="name" value="<?= e($old['name']) ?>" required>
                    <?php if (isset($errors['name'])): ?><em><?= e($errors['name']) ?></em><?php endif; ?>
                </label>
                <label class="field<?= isset($errors['email']) ? ' has-error' : '' ?>">
                    <span>E-mail</span>
                    <input type="email" name="email" value="<?= e($old['email']) ?>" required>
                    <?php if (isset($errors['email'])): ?><em><?= e($errors['email']) ?></em><?php endif; ?>
                </label>
            </div>

            <div class="field field--wide<?= isset($errors['type']) ? ' has-error' : '' ?>">
                <span>Тип обращения</span>
                <div class="chips">
<?php foreach (Message::TYPES as $key => $label): ?>
                    <label class="chip"><input type="radio" name="type" value="<?= $key ?>"<?= $old['type'] === $key ? ' checked' : '' ?>><span><?= $label ?></span></label>
<?php endforeach; ?>
                </div>
                <?php if (isset($errors['type'])): ?><em><?= e($errors['type']) ?></em><?php endif; ?>
            </div>

            <label class="field field--wide<?= isset($errors['text']) ? ' has-error' : '' ?>">
                <span>Текст обращения</span>
                <textarea name="text" rows="5" required><?= e($old['text']) ?></textarea>
                <?php if (isset($errors['text'])): ?><em><?= e($errors['text']) ?></em><?php endif; ?>
            </label>

            <div class="field field--wide<?= isset($errors['reply_via']) ? ' has-error' : '' ?>">
                <span>Как вам ответить</span>
                <div class="chips">
<?php foreach (Message::REPLY_VIA as $key => $label): ?>
                    <label class="chip"><input type="checkbox" name="reply_via[]" value="<?= $key ?>"<?= in_array($key, $old['reply_via'], true) ? ' checked' : '' ?>><span><?= $label ?></span></label>
<?php endforeach; ?>
                </div>
                <?php if (isset($errors['reply_via'])): ?><em><?= e($errors['reply_via']) ?></em><?php endif; ?>
            </div>

            <button class="btn btn--accent" type="submit">Отправить</button>
        </form>
    </div>
</section>
