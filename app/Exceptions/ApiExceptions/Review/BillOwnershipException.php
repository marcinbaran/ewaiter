<?php

namespace App\Exceptions\ApiExceptions\Review;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class BillOwnershipException extends ApiException
{
    protected const ERROR = ApiError::BILL_NOT_BELONG_TO_USER;

    protected const STATUS = Response::HTTP_FORBIDDEN;
}
