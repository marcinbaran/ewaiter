<?php

namespace App\Exceptions\ApiExceptions\Order;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class NoAddressProvidedException extends ApiException
{
    protected const ERROR = ApiError::NO_ADDRESS_PROVIDED;

    protected const STATUS = Response::HTTP_CONFLICT;
}
