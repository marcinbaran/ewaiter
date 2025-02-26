<?php

namespace App\Exceptions\ApiExceptions\Delivery;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class ItemsCannotBeDeliveredException extends ApiException
{
    protected const ERROR = ApiError::ITEMS_CANNOT_BE_DELIVERED;

    protected const STATUS = Response::HTTP_CONFLICT;
}
