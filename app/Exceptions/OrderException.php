<?php

namespace App\Exceptions;

use RuntimeException;

class OrderException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    /**
     * Cart is empty exception.
     */
    public static function emptyCart(): self
    {
        return new self('Cart is empty or does not exist.');
    }

    /**
     * Product not found exception.
     */
    public static function productNotFound(int $productId): self
    {
        return new self("Product (ID: {$productId}) not found.");
    }

    /**
     * Insufficient stock exception.
     */
    public static function insufficientStock(string $productName, int $productId): self
    {
        return new self("Product '{$productName}' (ID: {$productId}) has insufficient stock.");
    }

    /**
     * Order not found exception.
     */
    public static function orderNotFound(int $orderId): self
    {
        return new self("Order (ID: {$orderId}) not found.");
    }

    /**
     * Invalid order status exception.
     */
    public static function invalidStatus(string $status): self
    {
        return new self("Invalid order status: {$status}.");
    }
}
