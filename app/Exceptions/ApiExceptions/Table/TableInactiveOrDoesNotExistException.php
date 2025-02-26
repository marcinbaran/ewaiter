<?php

namespace App\Exceptions\ApiExceptions\Table;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class TableInactiveOrDoesNotExistException extends ApiException
{
    protected const ERROR = ApiError::TABLE_INACTIVE_OR_DOES_NOT_EXIST;
    protected const STATUS = Response::HTTP_CONFLICT;

}
