<?php

namespace App\Exceptions\ApiExceptions\Delivery;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class DeliveryAddressExceedsTheRestaurantPolygon extends ApiException
{
    protected const ApiError ERROR = ApiError::DELIVERY_ADDRESS_EXCEEDS_THE_RESTAURANT_POLYGON;
    protected const int STATUS = Response::HTTP_UNPROCESSABLE_ENTITY;
}
