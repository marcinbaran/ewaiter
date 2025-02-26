<?php

namespace App\Exceptions\ApiExceptions\Address;

use  App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class InvalidPhoneNumberException extends ApiException
{
    protected const ERROR = ApiError::INVALID_PHONE_NUMBER;
    protected const STATUS = Response::HTTP_BAD_REQUEST;

}
