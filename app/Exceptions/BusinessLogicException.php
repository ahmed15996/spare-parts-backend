<?php

namespace App\Exceptions;

use Exception;

class BusinessLogicException extends Exception
{
    /**
     * Create a new business logic exception instance.
     *
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message = "Business logic error", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
