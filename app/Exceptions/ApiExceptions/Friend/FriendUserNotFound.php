<?php

namespace App\Exceptions\ApiExceptions\Friend;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class FriendUserNotFound extends ApiException
{
    protected const ERROR = ApiError::FRIEND_USER_NOT_FOUND;

    protected const STATUS = Response::HTTP_NOT_FOUND;
}
