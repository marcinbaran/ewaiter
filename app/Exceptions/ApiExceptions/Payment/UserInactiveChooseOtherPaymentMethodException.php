<?php

namespace App\Exceptions\ApiExceptions\Payment;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class UserInactiveChooseOtherPaymentMethodException extends ApiException
{
    protected const ERROR = ApiError::USER_INACTIVE_CHOOSE_OTHER_PAYMENT_METHOD;

    protected const STATUS = Response::HTTP_CONFLICT;
}
