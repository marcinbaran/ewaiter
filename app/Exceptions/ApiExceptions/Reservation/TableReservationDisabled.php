<?php

namespace App\Exceptions\ApiExceptions\Reservation;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class TableReservationDisabled extends ApiException
{
    protected const ERROR = ApiError::TABLE_RESERVATION_DISABLED;
    protected const STATUS = Response::HTTP_FORBIDDEN;

}
