<?php

namespace App\Interfaces;

interface FeedGeneratorInterface
{
    public function generate(array $products): string;

    public function contentType(): string;
}
