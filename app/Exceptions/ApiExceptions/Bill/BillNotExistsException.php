<?php

namespace App\Exceptions\ApiExceptions\Bill;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class BillNotExistsException extends ApiException
{
    protected const ERROR = ApiError::BILL_NOT_FOUND;
    protected const STATUS = Response::HTTP_NOT_FOUND;
}
