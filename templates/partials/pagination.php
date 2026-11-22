<?php /** @var App\Core\Paginator $pager */ ?>
<?php if ($pager->hasPages()): ?>
<nav class="pager" aria-label="Страницы">
<?php if ($pager->page > 1): ?>
    <a href="<?= e($pager->url($pager->page - 1)) ?>" class="pager__arrow">←</a>
<?php endif; ?>
<?php for ($i = 1; $i <= $pager->pages; $i++): ?>
    <a href="<?= e($pager->url($i)) ?>" class="pager__num<?= $i === $pager->page ? ' is-current' : '' ?>"><?= $i ?></a>
<?php endfor; ?>
<?php if ($pager->page < $pager->pages): ?>
    <a href="<?= e($pager->url($pager->page + 1)) ?>" class="pager__arrow">→</a>
<?php endif; ?>
</nav>
<?php endif; ?>
