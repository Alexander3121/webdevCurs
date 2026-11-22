<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Db;

/**
 * Автомобили автопарка.
 */
final class Car
{
    public const CLASSES = [
        'economy'  => 'Эконом',
        'comfort'  => 'Комфорт',
        'business' => 'Бизнес',
        'suv'      => 'Кроссовер',
        'minivan'  => 'Минивэн',
    ];

    public const TRANSMISSIONS = [
        'auto'   => 'Автомат',
        'manual' => 'Механика',
    ];

    public const SORTS = [
        'price_asc'  => ['label' => 'Сначала дешевле', 'sql' => 'price_per_day ASC, id ASC'],
        'price_desc' => ['label' => 'Сначала дороже',  'sql' => 'price_per_day DESC, id ASC'],
        'year_desc'  => ['label' => 'Сначала новые',   'sql' => 'year DESC, id ASC'],
        'name'       => ['label' => 'По названию',     'sql' => 'brand ASC, model ASC'],
    ];

    public static function find(int $id): ?array
    {
        return Db::get()->one('SELECT * FROM cars WHERE id = ?', [$id]);
    }

    /** Свободные машины для выпадающего списка */
    public static function available(): array
    {
        return Db::get()->all(
            'SELECT id, brand, model, class, price_per_day, deposit FROM cars WHERE is_available = 1 ORDER BY price_per_day, brand'
        );
    }

    /** Несколько машин для главной */
    public static function popular(int $limit): array
    {
        return Db::get()->all(
            'SELECT * FROM cars WHERE is_available = 1 ORDER BY year DESC, price_per_day DESC LIMIT ' . $limit
        );
    }

    /**
     * Каталог с фильтрами, поиском, сортировкой и пагинацией.
     * @param array{q?:string,class?:string,transmission?:string,sort?:string} $f
     * @return array{items:array,total:int}
     */
    public static function search(array $f, int $page, int $perPage): array
    {
        $where  = [];
        $params = [];

        // Поиск по словам: «toyota cam» найдёт Toyota Camry
        foreach (preg_split('/\s+/u', $f['q'] ?? '', -1, PREG_SPLIT_NO_EMPTY) as $word) {
            $where[]  = '(brand LIKE ? OR model LIKE ?)';
            $like     = '%' . $word . '%';
            array_push($params, $like, $like);
        }
        if (isset(self::CLASSES[$f['class'] ?? ''])) {
            $where[]  = 'class = ?';
            $params[] = $f['class'];
        }
        if (isset(self::TRANSMISSIONS[$f['transmission'] ?? ''])) {
            $where[]  = 'transmission = ?';
            $params[] = $f['transmission'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $orderSql = self::SORTS[$f['sort'] ?? '']['sql'] ?? self::SORTS['price_asc']['sql'];

        $total  = (int) Db::get()->value("SELECT COUNT(*) FROM cars $whereSql", $params);
        $offset = max(0, ($page - 1) * $perPage);

        $items = Db::get()->all(
            "SELECT * FROM cars $whereSql ORDER BY is_available DESC, $orderSql LIMIT $perPage OFFSET $offset",
            $params
        );

        return ['items' => $items, 'total' => $total];
    }

    public static function title(array $car): string
    {
        return $car['brand'] . ' ' . $car['model'];
    }
}
