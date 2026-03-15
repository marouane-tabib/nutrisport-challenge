<?php

namespace App\Interfaces;

interface FeedGeneratorInterface
{
    /**
     * Generate a feed from the given products.
     *
     * @param array $products The array of products to include in the feed
     * @return string The generated feed content as a string
     */
    public function generate(array $products): string;

    /**
     * Get the content type for this feed format.
     *
     * @return string The MIME type of the feed (e.g., 'application/rss+xml', 'application/atom+xml')
     */
    public function contentType(): string;
}
