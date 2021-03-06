<?php

//declare(strict_types = 1);

namespace HtmlParser;

use Exception;
use DOMNode;
use DOMDocument;
use DOMDocumentFragment;
use SimpleXMLElement;
use DOMXPath;

class Parser {
    public static function stringify(DOMNode $node) {
        if ($node instanceof DOMDocument) {
            return $node->saveHTML($node);
        }

        return $node->ownerDocument->saveHTML($node);
    }

    public static function parse($html) {
        $detected = mb_detect_encoding($html);

        if ($detected) {
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', $detected);
        }

        $document = self::createDOMDocument($html);
        $xpath = new DOMXPath($document);

        $charset = $xpath->query('.//meta[@charset]')->item(0);
        $httpEquiv = $xpath->query('.//meta[@http-equiv]')->item(0);

        if ($charset || $httpEquiv) {
            $charset = $charset ? self::stringify($charset) : null;
            $httpEquiv = $httpEquiv ? self::stringify($httpEquiv) : null;

            $html = preg_replace(
                '/<head[^>]*>/',
                '<head>' . $charset . $httpEquiv,
                $html
            );

            return self::createDOMDocument($html);
        }

        return $document;
    }

    public static function parseFragment($html) {
        $html = "<html><head></head><body>{$html}</body></html>";
        $document = static::parse($html);
        $fragment = $document->createDocumentFragment();

        $body = $document->getElementsByTagName('body')->item(0);

        $nodes = [];

        foreach ($body->childNodes as $node) {
            $nodes[] = $node;
        }

        foreach ($nodes as $node) {
            $fragment->appendChild($node);
        }

        return $fragment;
    }

    private static function createDOMDocument($code) {
        $errors = libxml_use_internal_errors(true);
        $entities = libxml_disable_entity_loader(true);

        $document = new DOMDocument();
        $document->loadHTML($code);

        libxml_use_internal_errors($errors);
        libxml_disable_entity_loader($entities);

        return $document;
    }
}
