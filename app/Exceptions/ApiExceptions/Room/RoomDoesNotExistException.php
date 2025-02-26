<?php

namespace App\Exceptions\ApiExceptions\Room;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class RoomDoesNotExistException extends ApiException
{
    protected const ERROR = ApiError::ROOM_DOES_NOT_EXIST;
    protected const STATUS = Response::HTTP_CONFLICT;

}
