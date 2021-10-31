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

namespace League\CommonMark\Delimiter;

use League\CommonMark\Node\Inline\AbstractStringContainer;

interface DelimiterInterface {
    public function canClose();

    public function canOpen();

    public function isActive();

    public function setActive($active);

    public function getChar();

    public function getIndex();

    public function getNext();

    public function setNext(DelimiterInterface $next = null);

    public function getLength();

    public function setLength($length);

    public function getOriginalLength();

    public function getInlineNode();

    public function getPrevious();

    public function setPrevious(DelimiterInterface $previous = null);
}
