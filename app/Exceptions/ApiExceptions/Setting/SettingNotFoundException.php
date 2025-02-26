<?php

namespace App\Exceptions\ApiExceptions\Setting;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class SettingNotFoundException extends ApiException
{
    protected const ERROR = ApiError::SETTING_NOT_FOUND;
    protected const STATUS = Response::HTTP_INTERNAL_SERVER_ERROR;

}
