<?php

namespace App\Exceptions\ApiExceptions\Order;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class MandatoryAdditionsNotSelectedException extends ApiException
{
    protected const ERROR = ApiError::MANDATORY_ADDITIONS_NOT_SELECTED;

    protected const STATUS = Response::HTTP_CONFLICT;
}
