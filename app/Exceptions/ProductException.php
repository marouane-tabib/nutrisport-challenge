<?php

namespace App\Exceptions;

use RuntimeException;

class ProductException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    /**
     * Unsupported feed format exception.
     */
    public static function unsupportedFeedFormat(string $format): self
    {
        return new self("Unsupported feed format: {$format}.");
    }
}
