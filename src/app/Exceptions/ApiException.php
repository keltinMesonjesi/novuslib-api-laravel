<?php

namespace App\Exceptions;

use Exception;

/**
 * Class that extends base Exception for throwing custom ones
 */
class ApiException extends Exception
{
    public function __construct(string $message = "", int $code = 0)
    {
        parent::__construct($message, $code);
    }
}