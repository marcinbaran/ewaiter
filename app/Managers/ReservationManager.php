<?php

namespace App\Managers;

use App\Enum\ReservationStatus;
use App\Exceptions\SimpleValidationException;
use App\Http\Controllers\ParametersTrait;
use App\Models\Reservation;
use App\Models\Table;
use App\Services\TranslationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationManager
{
    use ParametersTrait;

    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @param TranslationService $service
     */
    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
    }

    /**
     * @param Request $request
     *
     * @return Reservation
     */
    public function create(Request $request): Reservation
    {
        $params = $this->getParams($request, ['status' => ReservationStatus::PENDING->value, 'table_id', 'user_id', 'name', 'people_number', 'start', 'phone', 'description']);
        $params = $this->hydrateActiveAndCancelValues($params);

        $this->checkWhetherTableIsAvailable($params);

        $reservation = DB::connection('tenant')->transaction(function () use ($params) {
            $user = Auth::user();
            $params['telephone'] = $params['phone'] ?? $user->phone;
            $params['user_id'] = $params['user_id'] ?? $user->id;
            $params['ticket'] = 1;
            $reservation = Reservation::create(Reservation::decamelizeArray($params))->fresh();

            return $reservation;
        });

        $request_notification = request();
        $request_notification->request->add(['type' => 'reservation', 'reservation' => ['id' => $reservation->id], 'url' => Route('admin.reservations.edit', ['reservation' => $reservation->id])]);

        NotificationManager::create($request_notification);

        return $reservation;
    }

    private function checkWhetherTableIsAvailable($params)
    {
        $tableId = $params['table_id'] ?? null;
        if ($tableId) {
            $table = Table::find($tableId);
            if (! $table->active) {
                throw new SimpleValidationException([__('validation.table_not_active')]);
            }
        }
    }

    /**
     * @param Request $request
     * @param Reservation $reservation
     *
     * @return Reservation
     */
    public function update(Request $request, Reservation $reservation): Reservation
    {
        $params = $this->getParams($request, ['status' => ReservationStatus::PENDING->value, 'table_id', 'user_id', 'name', 'people_number', 'start', 'phone', 'description']);
        $params = $this->hydrateActiveAndCancelValues($params);

        $this->checkWhetherTableIsAvailable($params);

        DB::connection('tenant')->transaction(function () use ($params, $reservation) {
            if (! empty($params)) {
                $this->checkWhetherTableIsAvailable($params);
                $reservation->update($params);
            }
        });

        return $reservation;
    }

    /**
     * @param Reservation $reservation
     *
     * @return Reservation
     */
    public function delete(Reservation $reservation): Reservation
    {
        DB::connection('tenant')->transaction(function () use ($reservation) {
            $reservation->delete();
        });

        return $reservation;
    }

    /**
     * @param Request $request
     * @param Reservation $reservation
     *
     * @return bool
     */
    public function checkAvailability(Request $request, Reservation $reservation): bool
    {
        $table_id = $request->get('table_id');
        $currentReservationId = $reservation->id;
        $bufferHours = 2;
        $newReservationStart = Carbon::parse($reservation->start);

        $windowStart = $newReservationStart->copy()->subHours($bufferHours);
        $windowEnd = $newReservationStart->copy()->addHours($bufferHours);

        $conflictingReservations = Reservation::where('table_id', $table_id)
            ->where('id', '!=', $currentReservationId)
            ->where('active', 1)
            ->where('closed', 0)
            ->whereBetween('start', [$windowStart, $windowEnd])
            ->exists();

        return !$conflictingReservations;
    }

    /**
     * @param Request $request
     * @param Reservation $reservation
     *
     * @return bool
     */
    public function checkTableInAvailability(Request $request): bool
    {
        $table_id = $request->get('table_id');
        $reservations = Reservation::
        whereNotNull('table_id')
            ->where('table_id', $table_id)
            ->where('active', 1)
            ->where('closed', 0)
            ->whereDate('start', '=', Carbon::parse($request->get('start'))->format('Y-m-d'))
            ->get();
        if (count($reservations)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function checkTableTooSmall(Request $request): bool
    {
        $table_id = $request->get('table_id');
        $people_number = $request->get('people_number');
        $table = Table::
        where('id', $table_id)
            ->first();
        if ($table && $table->people_number && ($table->people_number < $people_number)) {
            return true;
        } else {
            return false;
        }
    }

    private function hydrateActiveAndCancelValues($params): array
    {
        $reservationStatus = ReservationStatus::from($params['status']);

        if ($reservationStatus === ReservationStatus::CANCELLED) {
            $params['closed'] = true;
            $params['active'] = false;
        } elseif ($reservationStatus === ReservationStatus::CONFIRMED) {
            $params['closed'] = false;
            $params['active'] = true;
        } elseif ($reservationStatus === ReservationStatus::PENDING) {
            $params['closed'] = false;
            $params['active'] = false;
        }

        return $params;
    }
}
