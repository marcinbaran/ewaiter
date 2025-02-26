<?php

namespace App\Exceptions\ApiExceptions\Addition;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class AdditionDetailsHasChangedException extends ApiException
{
    protected const ERROR = ApiError::ADDITION_DETAILS_HAVE_CHANGED;
    protected const STATUS = Response::HTTP_CONFLICT;

}
