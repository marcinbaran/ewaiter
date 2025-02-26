<?php

namespace App\DTO;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Response;
use JsonSerializable;

class ApiError implements Arrayable, Jsonable, JsonSerializable
{
    final protected const ERROR_CODE_KEY = 'error';

    final protected const ERROR_DETAILS_KEY = 'details';

    final protected const ERROR_CONTEXT_KEY = 'context';

    final protected const UNJSONABLE_DATA_EXCEPTION_MESSAGE = 'Received data could not be converted to JSON format';

    public function __construct(
        protected string $errorMessage,
        protected string $errorCode,
        protected array $context,
    ) {
    }

    public function toArray(): array
    {
        return [
            self::ERROR_CODE_KEY    => $this->errorCode,
            self::ERROR_DETAILS_KEY => $this->errorMessage,
            self::ERROR_CONTEXT_KEY => $this->context,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toJson($options = 0): string
    {
        $jsonEncoded = json_encode($this->jsonSerialize(), $options);

        if ($jsonEncoded !== false) {
            return $jsonEncoded;
        }

        throw new \Exception(self::UNJSONABLE_DATA_EXCEPTION_MESSAGE, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
