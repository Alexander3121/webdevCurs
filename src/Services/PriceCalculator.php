<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Расчёт стоимости аренды по параметрам.
 * Используется и страницей калькулятора, и JSON-эндпоинтом, и при бронировании,
 * поэтому сумма везде получается одинаковой.
 */
final class PriceCalculator
{
    /** Скидки за длительность: от N суток => процент */
    public const DISCOUNTS = [30 => 25, 14 => 15, 7 => 10];

    /** Надбавка для молодых / неопытных водителей, % */
    public const YOUNG_SURCHARGE = 20;
    public const YOUNG_AGE = 23;
    public const YOUNG_EXPERIENCE = 2;

    public const MAX_DAYS = 90;

    /**
     * Дополнительные опции.
     * per_day — цена за сутки, fixed — разово, percent — % от стоимости аренды
     */
    public const OPTIONS = [
        'child_seat'   => ['label' => 'Детское кресло',         'per_day' => 300],
        'gps'          => ['label' => 'Навигатор',              'per_day' => 200],
        'extra_driver' => ['label' => 'Дополнительный водитель', 'per_day' => 400],
        'insurance'    => ['label' => 'Полная страховка (КАСКО)', 'percent' => 15],
        'delivery'     => ['label' => 'Подача машины по адресу', 'fixed' => 1500],
    ];

    private array $errors = [];

    /**
     * @param array    $car      строка из таблицы cars (нужны price_per_day и deposit)
     * @param string   $from     дата начала, Y-m-d
     * @param string   $to       дата окончания, Y-m-d
     * @param string[] $options  коды опций из OPTIONS
     * @return array|null        расчёт или null, если есть ошибки (см. errors())
     */
    public function calculate(array $car, string $from, string $to, array $options, int $age, int $experience): ?array
    {
        $this->errors = [];

        $days = $this->countDays($from, $to);

        if ($age < 18 || $age > 80) {
            $this->errors[] = 'Возраст водителя должен быть от 18 до 80 лет.';
        } elseif ($experience < 0 || $experience > $age - 18) {
            $this->errors[] = 'Стаж не может быть больше, чем (возраст − 18) лет.';
        }
        if ($this->errors) {
            return null;
        }

        $pricePerDay = (int) $car['price_per_day'];
        $base = $pricePerDay * $days;

        // Скидка за длительную аренду
        $discountPercent = 0;
        foreach (self::DISCOUNTS as $minDays => $percent) {
            if ($days >= $minDays) {
                $discountPercent = $percent;
                break;
            }
        }
        $discount = (int) round($base * $discountPercent / 100);
        $rental = $base - $discount;

        // Надбавка для молодого водителя
        $isYoung = $age < self::YOUNG_AGE || $experience < self::YOUNG_EXPERIENCE;
        $surcharge = $isYoung ? (int) round($rental * self::YOUNG_SURCHARGE / 100) : 0;

        // Опции
        $lines = [];
        foreach (array_unique($options) as $code) {
            if (!isset(self::OPTIONS[$code])) {
                continue;
            }
            $opt = self::OPTIONS[$code];
            if (isset($opt['per_day'])) {
                $sum  = $opt['per_day'] * $days;
                $note = money($opt['per_day']) . ' × ' . $days . ' ' . plural($days, 'сутки', 'суток', 'суток');
            } elseif (isset($opt['percent'])) {
                $sum  = (int) round($rental * $opt['percent'] / 100);
                $note = $opt['percent'] . '% от аренды';
            } else {
                $sum  = $opt['fixed'];
                $note = 'разово';
            }
            $lines[] = ['code' => $code, 'label' => $opt['label'], 'note' => $note, 'sum' => $sum];
        }
        $optionsTotal = array_sum(array_column($lines, 'sum'));

        $total = $rental + $surcharge + $optionsTotal;

        return [
            'days'             => $days,
            'price_per_day'    => $pricePerDay,
            'base'             => $base,
            'discount_percent' => $discountPercent,
            'discount'         => $discount,
            'rental'           => $rental,
            'young'            => $isYoung,
            'surcharge'        => $surcharge,
            'options'          => $lines,
            'options_total'    => $optionsTotal,
            'total'            => $total,
            'avg_per_day'      => (int) round($total / $days),
            'deposit'          => (int) $car['deposit'],
        ];
    }

    public function errors(): array
    {
        return $this->errors;
    }

    /** Количество суток между датами (минимум 1). Ошибки складываются в $this->errors */
    private function countDays(string $from, string $to): int
    {
        $start = \DateTimeImmutable::createFromFormat('!Y-m-d', $from);
        $end   = \DateTimeImmutable::createFromFormat('!Y-m-d', $to);

        if (!$start || !$end || $start->format('Y-m-d') !== $from || $end->format('Y-m-d') !== $to) {
            $this->errors[] = 'Укажите корректные даты получения и возврата.';
            return 1;
        }

        $today = new \DateTimeImmutable('today');
        if ($start < $today) {
            $this->errors[] = 'Дата получения не может быть в прошлом.';
        }
        if ($end < $start) {
            $this->errors[] = 'Дата возврата должна быть не раньше даты получения.';
            return 1;
        }

        $days = max(1, (int) $start->diff($end)->days);
        if ($days > self::MAX_DAYS) {
            $this->errors[] = 'Максимальный срок аренды — ' . self::MAX_DAYS . ' суток.';
        }
        return $days;
    }
}
