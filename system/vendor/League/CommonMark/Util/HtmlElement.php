<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Util;

class HtmlElement {
    /**
     * @var string
     */
    protected $tagName;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var HtmlElement|HtmlElement[]|string
     */
    protected $contents;

    /**
     * @var bool
     */
    protected $selfClosing = false;

    /**
     * @param string                                $tagName     Name of the HTML tag
     * @param array<string, string|string[]|bool>   $attributes  Array of attributes (values should be unescaped)
     * @param HtmlElement|HtmlElement[]|string|null $contents    Inner contents, pre-escaped if needed
     * @param bool                                  $selfClosing Whether the tag is self-closing
     */
    public function __construct($tagName, array $attributes = [], $contents = '', $selfClosing = false) {
        $this->tagName = $tagName;
        $this->selfClosing = $selfClosing;

        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }

        $this->setContents($contents ?: '');
    }

    public function getTagName() {
        return $this->tagName;
    }

    /**
     * @return array
     */
    public function getAllAttributes() {
        return $this->attributes;
    }

    /**
     * @return string|bool|null
     *
     * @param mixed $key
     */
    public function getAttribute($key) {
        if (!isset($this->attributes[$key])) {
            return null;
        }

        return $this->attributes[$key];
    }

    /**
     * @param string|string[]|bool $value
     * @param mixed                $key
     */
    public function setAttribute($key, $value) {
        if (\is_array($value)) {
            $this->attributes[$key] = \implode(' ', \array_unique($value));
        } else {
            $this->attributes[$key] = $value;
        }

        return $this;
    }

    /**
     * @return HtmlElement|HtmlElement[]|string
     *
     * @param mixed $asString
     */
    public function getContents($asString = true) {
        if (!$asString) {
            return $this->contents;
        }

        return $this->getContentsAsString();
    }

    /**
     * Sets the inner contents of the tag (must be pre-escaped if needed)
     *
     * @param HtmlElement|HtmlElement[]|string $contents
     *
     * @return $this
     */
    public function setContents($contents) {
        $this->contents = $contents ? $contents : '';

        return $this;
    }

    public function __toString() {
        $result = '<' . $this->tagName;

        foreach ($this->attributes as $key => $value) {
            if ($value === true) {
                $result .= ' ' . $key;
            } elseif ($value === false) {
                continue;
            } else {
                $result .= ' ' . $key . '="' . Xml::escape($value) . '"';
            }
        }

        if ($this->contents !== '') {
            $result .= '>' . $this->getContentsAsString() . '</' . $this->tagName . '>';
        } elseif ($this->selfClosing && $this->tagName === 'input') {
            $result .= '>';
        } elseif ($this->selfClosing) {
            $result .= ' />';
        } else {
            $result .= '></' . $this->tagName . '>';
        }

        return $result;
    }

    private function getContentsAsString() {
        if (\is_string($this->contents)) {
            return $this->contents;
        }

        if (\is_array($this->contents)) {
            return \implode('', $this->contents);
        }

        return (string) $this->contents;
    }
}
