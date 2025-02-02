<?php

namespace App\Exceptions;

use Exception;

class RateLimitException extends Exception
{
    protected $status_code = 429;
    protected $message = "Too many attempts! Please try again later";

    public function __construct(string $message, int $status_code)
    {
        parent::__construct($message, $status_code);
        $this->status_code = $status_code;
        $this->message = $message;
    }

    public function showMessage()
    {
        return $this->message;
    }

    public function showStatusCode()
    {
        return $this->status_code;
    }

}