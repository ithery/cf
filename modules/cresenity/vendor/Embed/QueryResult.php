<?php

//declare(strict_types=1);

namespace Embed;

use Closure;
use DOMElement;
use DOMNodeList;
use Psr\Http\Message\UriInterface;

require_once dirname(__FILE__) . '/functions.php';

class QueryResult {

    private $extractor;
    private $nodes = [];

    public function __construct(DOMNodeList $result, Extractor $extractor) {
        $this->nodes = iterator_to_array($result, false);
        $this->extractor = $extractor;
    }

    public function node() {
        return isset($this->nodes[0]) ? $this->nodes[0] : null;
    }

    public function nodes() {
        return $this->nodes;
    }

    public function filter(Closure $callback) {
        $this->nodes = array_filter($this->nodes, $callback);

        return $this;
    }

    public function get($attribute = null) {
        $node = $this->node();

        if (!$node) {
            return null;
        }

        return $attribute ? self::getAttribute($node, $attribute) : $node->nodeValue;
    }

    public function getAll($attribute = null) {
        $nodes = $this->nodes();

        return array_filter(
                array_map(function($node) use ($attrribute) {
                    return $attribute ? self::getAttribute($node, $attribute) : $node->nodeValue;
                }, $nodes)
        );
    }

    public function str($attribute = null) {
        $value = $this->get($attribute);

        return $value ? clean($value) : null;
    }

    public function strAll($attribute = null) {
        return array_filter(array_map(function($value) {
            return clean($value);
            
        }, $this->getAll($attribute)));
    }

    public function int($attribute = null) {
        $value = $this->get($attribute);

        return $value ? (int) $value : null;
    }

    public function url($attribute = null) {
        $value = $this->get($attribute);

        if (!$value) {
            return null;
        }

        try {
            return $this->extractor->resolveUri($value);
        } catch (Exception $error) {
            //do nothing
        }
        return null;
    }

    private static function getAttribute(DOMElement $node, $name) {
        //Don't use $node->getAttribute() because it does not work with namespaces (ex: xml:lang)
        $attributes = $node->attributes;

        for ($i = 0; $i < $attributes->length; ++$i) {
            $attribute = $attributes->item($i);

            if ($attribute->name === $name) {
                return $attribute->nodeValue;
            }
        }

        return null;
    }

}
