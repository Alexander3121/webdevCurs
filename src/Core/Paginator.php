<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Пагинация: считает количество страниц и строит ссылки с сохранением фильтров.
 */
final class Paginator
{
    public readonly int $pages;
    public readonly int $page;

    public function __construct(
        public readonly int $total,
        int $page,
        public readonly int $perPage,
        private string $path,
        private array $query = []
    ) {
        $this->pages = max(1, (int) ceil($total / $perPage));
        $this->page  = min(max(1, $page), $this->pages);
    }

    /** Номер страницы из ?page= */
    public static function currentPage(): int
    {
        $p = $_GET['page'] ?? 1;
        return is_numeric($p) ? max(1, (int) $p) : 1;
    }

    public function url(int $page): string
    {
        return url($this->path, $this->query + ['page' => $page > 1 ? $page : null]);
    }

    public function hasPages(): bool
    {
        return $this->pages > 1;
    }
}
