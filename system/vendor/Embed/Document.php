<?php

namespace Embed;

use DOMNode;
use DOMXPath;
use DOMDocument;
use RuntimeException;
use HtmlParser\Parser;
use Psr\Http\Message\UriInterface;
use Symfony\Component\CssSelector\CssSelectorConverter;

class Document {
    private static $cssConverter;

    private $extractor;

    private $document;

    private $xpath;

    public function __construct(Extractor $extractor) {
        $this->extractor = $extractor;

        $html = (string) $extractor->getResponse()->getBody();
        $html = str_replace('<br>', "\n<br>", $html);
        $html = str_replace('<br ', "\n<br ", $html);

        $this->document = !empty($html) ? Parser::parse($html) : new DOMDocument();
        $this->initXPath();
    }

    private function initXPath() {
        $this->xpath = new DOMXPath($this->document);
        $this->xpath->registerNamespace('php', 'http://php.net/xpath');
        $this->xpath->registerPhpFunctions();
    }

    public function __clone() {
        $this->document = clone $this->document;
        $this->initXPath();
    }

    public function remove($query) {
        $nodes = iterator_to_array($this->xpath->query($query), false);

        foreach ($nodes as $node) {
            $node->parentNode->removeChild($node);
        }
    }

    public function removeCss($query) {
        $this->remove(self::cssToXpath($query));
    }

    public function getDocument() {
        return $this->document;
    }

    /**
     * Helper to build xpath queries easily and case insensitive.
     *
     * @param mixed $startQuery
     */
    private static function buildQuery($startQuery, array $attributes) {
        $selector = [$startQuery];

        foreach ($attributes as $name => $value) {
            $selector[] = sprintf('[php:functionString("strtolower", @%s)="%s"]', $name, mb_strtolower($value));
        }

        return implode('', $selector);
    }

    /**
     * Select a element in the dom.
     *
     * @param mixed $query
     */
    public function select($query, array $attributes = null, DOMNode $context = null) {
        if (!empty($attributes)) {
            $query = self::buildQuery($query, $attributes);
        }

        return new QueryResult($this->xpath->query($query, $context), $this->extractor);
    }

    /**
     * Select a element in the dom using a css selector.
     *
     * @param mixed $query
     */
    public function selectCss($query, DOMNode $context = null) {
        return $this->select(self::cssToXpath($query), null, $context);
    }

    /**
     * Shortcut to select a <link> element and return the href.
     *
     * @param mixed $rel
     */
    public function link($rel, array $extra = []) {
        return $this->select('.//link', ['rel' => $rel] + $extra)->url('href');
    }

    public function __toString() {
        return Parser::stringify($this->getDocument());
    }

    private static function cssToXpath($selector) {
        if (!isset(self::$cssConverter)) {
            if (!class_exists(CssSelectorConverter::class)) {
                throw new RuntimeException('You need to install "symfony/css-selector" to use css selectors');
            }

            self::$cssConverter = new CssSelectorConverter();
        }

        return self::$cssConverter->toXpath($selector);
    }
}
