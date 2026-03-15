<?php

namespace App\Exceptions;

use Exception;

class ReportException extends Exception
{
    public static function adminEmailNotFound(): self
    {
        return new self('Admin agent with ID 1 has no email.');
    }
}
