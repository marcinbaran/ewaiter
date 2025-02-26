<?php

namespace App\Exceptions\ApiExceptions\FCMToken;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class TokenNotFound extends ApiException
{
    protected const ERROR = ApiError::FCM_TOKEN_NOT_FOUND;

    protected const STATUS = Response::HTTP_NOT_FOUND;
}
