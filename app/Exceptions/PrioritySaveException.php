<?php

namespace App\Exceptions;

use Exception;

class PrioritySaveException extends Exception
{
    protected int $status;

    public function __construct(string $message = '', int $status = 500, \Throwable $previous = null)
    {
        parent::__construct($message, $status, $previous);
        $this->status = $status;
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}
