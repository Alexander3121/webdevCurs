<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Paginator;
use App\Models\Car;
use App\Services\PriceCalculator;

final class CarsController extends Controller
{
    /** Каталог: фильтры, поиск, сортировка, пагинация */
    public function index(): void
    {
        $filters = [
            'q'            => trim((string) ($_GET['q'] ?? '')),
            'class'        => (string) ($_GET['class'] ?? ''),
            'transmission' => (string) ($_GET['transmission'] ?? ''),
            'sort'         => (string) ($_GET['sort'] ?? 'price_asc'),
        ];
        if (!isset(Car::SORTS[$filters['sort']])) {
            $filters['sort'] = 'price_asc';
        }

        $perPage = (int) App::config('per_page.cars', 6);
        $page    = Paginator::currentPage();

        $result = Car::search($filters, $page, $perPage);
        $pager  = new Paginator($result['total'], $page, $perPage, 'cars', $filters);

        // Страница вне диапазона — показываем последнюю
        if ($page > $pager->pages && $result['total'] > 0) {
            $result = Car::search($filters, $pager->page, $perPage);
        }

        $this->render('cars/index', [
            'title'   => 'Каталог автомобилей',
            'cars'    => $result['items'],
            'pager'   => $pager,
            'filters' => $filters,
        ]);
    }

    /** Карточка машины + форма бронирования с расчётом */
    public function show(int $id): void
    {
        $car = Car::find($id);
        if (!$car) {
            $this->notFound('Такой машины нет в каталоге');
            return;
        }

        $this->render('cars/show', [
            'title'   => Car::title($car) . ' — аренда',
            'car'     => $car,
            'options' => PriceCalculator::OPTIONS,
            'form'    => CalculatorController::defaultForm($car['id']),
            'errors'  => [],
        ]);
    }
}
