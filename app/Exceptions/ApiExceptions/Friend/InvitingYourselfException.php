<?php

namespace App\Exceptions\ApiExceptions\Friend;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class InvitingYourselfException extends ApiException
{
    protected const ERROR = ApiError::INVITING_YOURSELF;

    protected const STATUS = Response::HTTP_BAD_REQUEST;
}
