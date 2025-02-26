<?php

namespace App\Exceptions\ApiExceptions\Voucher;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class VoucherAlreadyUsedException extends ApiException
{
    protected const ERROR = ApiError::VOUCHER_USED;

    protected const STATUS = Response::HTTP_CONFLICT;
}
