<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\Car;
use App\Services\PriceCalculator;

final class CalculatorController extends Controller
{
    /** Значения формы по умолчанию: завтра на 3 суток */
    public static function defaultForm(int $carId = 0): array
    {
        return [
            'car'     => $carId,
            'from'    => date('Y-m-d', strtotime('+1 day')),
            'to'      => date('Y-m-d', strtotime('+4 days')),
            'options' => [],
            'age'     => 25,
            'exp'     => 5,
            'comment' => '',
        ];
    }

    /** Чтение параметров расчёта из массива запроса ($_GET или $_POST) */
    public static function readForm(array $src): array
    {
        $options = $src['options'] ?? [];
        $options = is_array($options) ? array_values(array_filter($options, 'is_string')) : [];

        return [
            'car'     => (int) ($src['car'] ?? 0),
            'from'    => is_string($src['from'] ?? null) ? $src['from'] : '',
            'to'      => is_string($src['to'] ?? null) ? $src['to'] : '',
            'options' => $options,
            'age'     => (int) ($src['age'] ?? 0),
            'exp'     => (int) ($src['exp'] ?? 0),
            'comment' => is_string($src['comment'] ?? null) ? trim($src['comment']) : '',
        ];
    }

    /**
     * Общий расчёт для страницы, API и бронирования.
     * @return array{0:?array,1:array,2:?array} [расчёт, ошибки, машина]
     */
    public static function calculateFor(array $form): array
    {
        $car = $form['car'] ? Car::find($form['car']) : null;
        if (!$car) {
            return [null, ['Выберите автомобиль.'], null];
        }

        $calc   = new PriceCalculator();
        $result = $calc->calculate($car, $form['from'], $form['to'], $form['options'], $form['age'], $form['exp']);

        return [$result, $calc->errors(), $car];
    }

    /** Страница калькулятора: расчёт по GET-параметрам */
    public function index(): void
    {
        $submitted = isset($_GET['car']);
        $form = $submitted ? self::readForm($_GET) : self::defaultForm();

        $result = null;
        $errors = [];
        $car    = null;
        if ($submitted) {
            [$result, $errors, $car] = self::calculateFor($form);
        }

        $this->render('calculator', [
            'title'   => 'Калькулятор стоимости аренды',
            'cars'    => Car::available(),
            'options' => PriceCalculator::OPTIONS,
            'form'    => $form,
            'result'  => $result,
            'errors'  => $errors,
            'car'     => $car,
        ]);
    }

    /** JSON для JavaScript: пересчёт без перезагрузки страницы */
    public function api(): void
    {
        [$result, $errors] = self::calculateFor(self::readForm($_GET));

        if ($result === null) {
            View::json(['ok' => false, 'errors' => $errors], 422);
            return;
        }

        View::json([
            'ok'   => true,
            'html' => View::fetch('partials/price', ['result' => $result]),
            'total'=> $result['total'],
        ]);
    }
}
