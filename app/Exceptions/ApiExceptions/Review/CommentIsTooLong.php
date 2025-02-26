<?php

namespace App\Exceptions\ApiExceptions\Review;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class CommentIsTooLong extends ApiException
{
    protected const ERROR = ApiError::COMMENT_TOO_LONG;

    protected const STATUS = Response::HTTP_CONFLICT;
}
