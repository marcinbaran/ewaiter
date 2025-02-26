<?php

namespace App\Exceptions\ApiExceptions\Review;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class ReviewAlreadyAdded extends ApiException
{
    protected const ERROR = ApiError::REVIEW_ALREADY_ADDED;

    protected const STATUS = Response::HTTP_CONFLICT;
}
