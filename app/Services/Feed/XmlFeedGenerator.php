<?php

namespace App\Services\Feed;

use App\Interfaces\FeedGeneratorInterface;
use SimpleXMLElement;

class XmlFeedGenerator implements FeedGeneratorInterface
{
    /**
     * Generate an XML feed from the given products.
     *
     * @param array $products The array of products to include in the feed
     * @return string The generated XML feed content
     */
    public function generate(array $products): string
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><products/>');

        foreach ($products as $product) {
            $node = $xml->addChild('product');
            $node->addChild('id',    (string) $product['id']);
            $node->addChild('name',  htmlspecialchars($product['name']));
            $node->addChild('stock', (string) $product['stock']);
        }

        return $xml->asXML();
    }

    /**
     * Get the content type for XML feed.
     *
     * @return string The MIME type 'application/xml'
     */
    public function contentType(): string
    {
        return 'application/xml';
    }
}
