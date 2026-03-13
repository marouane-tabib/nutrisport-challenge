<?php

namespace App\Exceptions;

use RuntimeException;

class CartException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    /**
     * Cart not found exception.
     */
    public static function cartNotFound(string $cartId): self
    {
        return new self("Cart with ID {$cartId} does not exist.");
    }
}
