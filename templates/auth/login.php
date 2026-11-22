<?php
/**
 * @var string $login
 * @var string $error
 */
?>
<section class="wrap section narrow narrow--sm">
    <form class="panel form" method="post" action="<?= url('login') ?>">
        <h1>Вход</h1>
        <?= csrf_field() ?>

<?php if ($error): ?>
        <div class="flash flash--error"><?= e($error) ?></div>
<?php endif; ?>

        <label class="field field--wide">
            <span>Ник или e-mail</span>
            <input type="text" name="login" value="<?= e($login) ?>" required autofocus autocomplete="username">
        </label>
        <label class="field field--wide">
            <span>Пароль</span>
            <input type="password" name="password" required autocomplete="current-password">
        </label>

        <button class="btn btn--accent btn--block" type="submit">Войти</button>
        <p class="muted center">Нет аккаунта? <a href="<?= url('register') ?>">Зарегистрируйтесь</a></p>
        <p class="muted center small">Демо-доступ: <code>alex</code> / <code>demo12345</code></p>
    </form>
</section>
