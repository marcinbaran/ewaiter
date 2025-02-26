<?php

namespace App\Exceptions\ApiExceptions\Friend;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class InvitationAlreadySentException extends ApiException
{
    protected const ERROR = ApiError::INVITATION_ALREADY_SENT;

    protected const STATUS = Response::HTTP_BAD_REQUEST;
}
