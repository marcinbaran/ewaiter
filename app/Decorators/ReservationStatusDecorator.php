<?php

namespace App\Decorators;

use App\Enum\ReservationStatus;
use App\Models\Reservation;

class ReservationStatusDecorator
{
    public function decorate(Reservation $reservation)
    {
        $status = ReservationStatus::from($reservation->status);
        switch ($status) {
            case ReservationStatus::CANCELLED:
                $classes = 'bg-red-100 text-gray-900';
                $text = __('admin.reservations.statuses.cancelled');
                break;
            case ReservationStatus::CONFIRMED:
                $classes = 'bg-green-100 text-gray-900';
                $text = __('admin.reservations.statuses.confirmed');
                break;
            default:
            case ReservationStatus::PENDING:
                $classes = 'bg-yellow-100 text-gray-900';
                $text = __('admin.reservations.statuses.pending');
                break;
        }

        return view('admin.partials.decorators.reservation-status', ['classes' => $classes, 'text' => $text]);
    }
}
