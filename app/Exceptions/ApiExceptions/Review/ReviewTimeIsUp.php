<?php

namespace App\Exceptions\ApiExceptions\Review;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class ReviewTimeIsUp extends ApiException
{
    protected const ApiError ERROR = ApiError::TIME_IS_UP;

    protected const int STATUS = Response::HTTP_FORBIDDEN;
}
