<?php
/**
 * @var array $old
 * @var array $errors
 */
$fields = [
    'nickname'        => ['Ник (латиница, цифры, «_»)', 'text', 'username'],
    'email'           => ['E-mail', 'email', 'email'],
    'password'        => ['Пароль (минимум 8 символов)', 'password', 'new-password'],
    'password_repeat' => ['Повторите пароль', 'password', 'new-password'],
];
?>
<section class="wrap section narrow narrow--sm">
    <form class="panel form" method="post" action="<?= url('register') ?>">
        <h1>Регистрация</h1>
        <?= csrf_field() ?>

<?php foreach ($fields as $name => [$label, $type, $auto]): ?>
        <label class="field field--wide<?= isset($errors[$name]) ? ' has-error' : '' ?>">
            <span><?= $label ?></span>
            <input type="<?= $type ?>" name="<?= $name ?>" value="<?= $type === 'password' ? '' : e($old[$name] ?? '') ?>" required autocomplete="<?= $auto ?>">
            <?php if (isset($errors[$name])): ?><em><?= e($errors[$name]) ?></em><?php endif; ?>
        </label>
<?php endforeach; ?>

        <button class="btn btn--accent btn--block" type="submit">Создать аккаунт</button>
        <p class="muted center">Уже есть аккаунт? <a href="<?= url('login') ?>">Войти</a></p>
    </form>
</section>
