<?php

namespace App\Services\Feed;

use App\Interfaces\FeedGeneratorInterface;

class JsonFeedGenerator implements FeedGeneratorInterface
{
    /**
     * Generate a JSON feed from the given products.
     *
     * @param array $products The array of products to include in the feed
     * @return string The generated JSON feed content
     */
    public function generate(array $products): string
    {
        return json_encode(
            ['products' => $products],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Get the content type for JSON feed.
     *
     * @return string The MIME type 'application/json'
     */
    public function contentType(): string
    {
        return 'application/json';
    }
}
