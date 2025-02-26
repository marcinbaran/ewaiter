<?php

namespace App\Exceptions;

class NoCategoryCategoryNotFoundException extends \Exception
{
    /**
     * @var string
     */
    protected $message = 'No category not found';

    /**
     * @var int
     */
    protected $code = 404;

    public function __construct()
    {
        parent::__construct($this->message, $this->code);
    }
}
