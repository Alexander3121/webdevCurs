<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Booking;
use App\Models\Car;
use App\Services\PriceCalculator;

final class BookingController extends Controller
{
    /** Оформление заявки со страницы машины */
    public function store(int $carId): void
    {
        $this->requireLogin();
        $this->verifyCsrf();

        $form = CalculatorController::readForm($_POST);
        $form['car'] = $carId;

        [$result, $errors, $car] = CalculatorController::calculateFor($form);

        if ($car && !$car['is_available']) {
            $errors[] = 'Эта машина сейчас недоступна для бронирования.';
        }
        if (mb_strlen($form['comment']) > 500) {
            $errors[] = 'Комментарий — не длиннее 500 символов.';
        }

        if (!$car) {
            $this->notFound('Такой машины нет в каталоге');
            return;
        }

        if ($errors || $result === null) {
            // Показываем ту же страницу машины с ошибками и введёнными данными
            $this->render('cars/show', [
                'title'   => Car::title($car) . ' — аренда',
                'car'     => $car,
                'options' => PriceCalculator::OPTIONS,
                'form'    => $form,
                'errors'  => $errors,
            ], 422);
            return;
        }

        // Сумму всегда считаем на сервере — цене из браузера не доверяем
        $codes = array_column($result['options'], 'code');

        Booking::create([
            'user_id'    => Auth::id(),
            'car_id'     => $carId,
            'date_from'  => $form['from'],
            'date_to'    => $form['to'],
            'days'       => $result['days'],
            'options'    => implode(',', $codes),
            'driver_age' => $form['age'],
            'experience' => $form['exp'],
            'total'      => $result['total'],
            'comment'    => $form['comment'],
        ]);

        flash('success', sprintf(
            'Заявка на %s принята: %d %s, %s. Менеджер свяжется с вами для подтверждения.',
            Car::title($car),
            $result['days'],
            plural($result['days'], 'сутки', 'суток', 'суток'),
            money($result['total'])
        ));
        $this->redirect('account');
    }
}
