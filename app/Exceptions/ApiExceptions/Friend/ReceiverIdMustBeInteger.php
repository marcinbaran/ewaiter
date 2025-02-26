<?php

namespace App\Exceptions\ApiExceptions\Friend;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class ReceiverIdMustBeInteger extends ApiException
{
    protected const ERROR = ApiError::RECEIVER_ID_MUST_BE_INTEGER;

    protected const STATUS = Response::HTTP_BAD_REQUEST;
}
