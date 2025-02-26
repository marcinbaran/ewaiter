<?php

namespace App\Exceptions;

use Exception;

class SimpleValidationException extends Exception
{
    public function __construct(private ?array $errors = [])
    {
        parent::__construct('Simple validation failed', 500);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
