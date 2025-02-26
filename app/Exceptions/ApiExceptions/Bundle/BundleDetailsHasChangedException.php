<?php

namespace App\Exceptions\ApiExceptions\Bundle;

use App\Enum\ApiError;
use App\Exceptions\ApiExceptions\ApiException;
use Illuminate\Http\Response;

class BundleDetailsHasChangedException extends ApiException
{
    protected const ERROR = ApiError::BUNDLE_DETAILS_HAVE_CHANGED;
    protected const STATUS = Response::HTTP_CONFLICT;
}
