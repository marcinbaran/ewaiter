<?php

namespace App\Exceptions\ApiExceptions\Voucher;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class InvalidVoucherCodeException extends ApiException
{
    protected const ERROR = ApiError::INVALID_VOUCHER_CODE;

    protected const STATUS = Response::HTTP_CONFLICT;
}
