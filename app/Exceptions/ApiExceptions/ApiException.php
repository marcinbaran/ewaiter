<?php

namespace App\Exceptions\ApiExceptions;

use App\DTO\ApiError as ApiErrorDTO;
use App\Enum\ApiError;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class ApiException extends Exception
{
    protected const STATUS = null;

    protected const ERROR = null;

    final protected const MUST_OVERRIDE_STATUS_EXCEPTION_MESSAGE = 'Exception must override STATUS constant with integer value';

    final protected const MUST_OVERRIDE_ERROR_EXCEPTION_MESSAGE = 'Exception must override ERROR constant with '.ApiError::class.' enum case';

    public function __construct(protected array $context = [])
    {
        parent::__construct($this->getErrorMessage(), $this->getStatus());
    }

    public function render(Request $request): Response
    {
        $error = new ApiErrorDTO($this->getErrorMessage(), $this->getErrorCode(), $this->getContext());

        return response($error, $this->getStatus());
    }

    public function getStatus(): int
    {
        if (static::STATUS !== null) {
            return static::STATUS;
        }

        throw new Exception(self::MUST_OVERRIDE_STATUS_EXCEPTION_MESSAGE, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function getErrorMessage(): string
    {
        if (static::ERROR instanceof ApiError) {
            return static::ERROR->value;
        }

        throw new Exception(self::MUST_OVERRIDE_ERROR_EXCEPTION_MESSAGE, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function getErrorCode(): string
    {
        if (static::ERROR instanceof ApiError) {
            return static::ERROR->name;
        }

        throw new Exception(self::MUST_OVERRIDE_ERROR_EXCEPTION_MESSAGE, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
