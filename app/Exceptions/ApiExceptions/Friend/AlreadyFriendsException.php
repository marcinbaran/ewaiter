<?php

namespace App\Exceptions\ApiExceptions\Friend;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class AlreadyFriendsException extends ApiException
{
    protected const ERROR = ApiError::ALREADY_FRIENDS;

    protected const STATUS = Response::HTTP_BAD_REQUEST;
}
