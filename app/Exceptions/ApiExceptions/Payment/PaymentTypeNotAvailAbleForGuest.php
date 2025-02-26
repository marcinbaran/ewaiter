<?php

namespace App\Exceptions\ApiExceptions\Payment;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class PaymentTypeNotAvailAbleForGuest extends ApiException
{
    protected const ERROR = ApiError::PAYMENT_TYPE_NOT_AVAILABLE_FOR_GUEST;

    protected const STATUS = Response::HTTP_CONFLICT;
}
