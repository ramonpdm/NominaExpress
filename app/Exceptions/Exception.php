<?php

namespace App\Exceptions;

use Throwable;
use Exception as BaseException;

class Exception extends BaseException
{
    public function __construct($message, $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
