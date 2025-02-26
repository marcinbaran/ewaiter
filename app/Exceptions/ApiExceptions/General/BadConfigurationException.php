<?php

namespace App\Exceptions\ApiExceptions\General;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class BadConfigurationException extends ApiException
{
    public const string MISSING_CONFIG_ENTRY_KEY = 'missing_config_entry';

    protected const ERROR = ApiError::BAD_CONFIGURATION;
    protected const STATUS = Response::HTTP_INTERNAL_SERVER_ERROR;

}
