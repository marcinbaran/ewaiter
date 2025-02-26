<?php

namespace App\Exceptions\ApiExceptions\Dish;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class DishDetailsHasChangedException extends ApiException
{
    protected const ERROR = ApiError::DISH_DETAILS_HAVE_CHANGED;
    protected const STATUS = Response::HTTP_CONFLICT;

}
