<?php

namespace App\Exceptions\ApiExceptions\Notifications;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class TooManyWaiterCallsException extends ApiException
{
    protected const ERROR = ApiError::TOO_MANY_WAITER_CALLS;

    protected const STATUS = Response::HTTP_TOO_MANY_REQUESTS;
}
