<?php

namespace App\Services\Feed;

use App\Interfaces\FeedGeneratorInterface;
use SimpleXMLElement;

class XmlFeedGenerator implements FeedGeneratorInterface
{
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

    public function contentType(): string
    {
        return 'application/xml';
    }
}
