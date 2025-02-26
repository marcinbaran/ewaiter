<?php

namespace App\Exceptions\ApiExceptions\Friend;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class UserWaitsForYourAccept extends ApiException
{
    protected const ERROR = ApiError::USER_WAITS_FOR_YOUR_ACCEPT;

    protected const STATUS = Response::HTTP_BAD_REQUEST;
}
