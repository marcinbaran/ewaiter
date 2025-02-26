<?php

namespace App\Exceptions\ApiExceptions\Delivery;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class DeliveryAddressExceedsTheRestaurantRange extends ApiException // FIXME: add 'Exception' suffix to class name
{
    protected const ERROR = ApiError::DELIVERY_ADDRESS_EXCEEDS_THE_RESTAURANT_RANGE;

    protected const STATUS = Response::HTTP_CONFLICT;
}
