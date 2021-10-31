<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Parser\Inline;

final class InlineParserMatch {
    /**
     * @var string
     */
    private $regex;

    private function __construct($regex) {
        $this->regex = $regex;
    }

    /**
     * @internal
     */
    public function getRegex() {
        return '/' . $this->regex . '/i';
    }

    /**
     * Match the given string (case-insensitive)
     *
     * @param mixed $str
     */
    public static function string($str) {
        return new self(\preg_quote($str, '/'));
    }

    /**
     * Match any of the given strings (case-insensitive)
     */
    public static function oneOf(...$str) {
        return new self(\implode('|', \array_map(static function ($str) {
            return \preg_quote($str, '/');
        }, $str)));
    }

    /**
     * Match a partial regular expression without starting/ending delimiters, anchors, or flags
     *
     * @param mixed $regex
     */
    public static function regex($regex) {
        return new self($regex);
    }

    public static function join(self ...$definitions) {
        $regex = '';
        foreach ($definitions as $definition) {
            $regex .= '(' . $definition->regex . ')';
        }

        return new self($regex);
    }
}
