<?php

namespace App\Services\Feed;

use App\Interfaces\FeedGeneratorInterface;

class JsonFeedGenerator implements FeedGeneratorInterface
{
    public function generate(array $products): string
    {
        return json_encode(
            ['products' => $products],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }

    public function contentType(): string
    {
        return 'application/json';
    }
}
