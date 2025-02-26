<?php

namespace App\Exceptions\ApiExceptions\Review;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class CannotEditCommentException extends ApiException
{
    protected const ApiError ERROR = ApiError::REVIEW_COMMENT_EDITED;

    protected const int STATUS = Response::HTTP_FORBIDDEN;
}
