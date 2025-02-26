<?php

namespace App\Exceptions\ApiExceptions\Review;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class OffensiveReviewException extends ApiException
{
    protected const ApiError ERROR = ApiError::OFFENSIVE_REVIEW;

    protected const int STATUS = Response::HTTP_UNPROCESSABLE_ENTITY;
}
