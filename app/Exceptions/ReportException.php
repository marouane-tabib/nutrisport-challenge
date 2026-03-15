<?php

namespace App\Exceptions;

use Exception;

class ReportException extends Exception
{
    /**
     * Thrown when the admin agent with ID 1 has no email address configured.
     *
     * @return self
     */
    public static function adminEmailNotFound(): self
    {
        return new self('Admin agent with ID 1 has no email.');
    }
}
