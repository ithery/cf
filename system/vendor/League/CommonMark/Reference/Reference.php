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

namespace League\CommonMark\Reference;

/**
 * @psalm-immutable
 */
final class Reference implements ReferenceInterface {
    /**
     * @var string
     *
     * @psalm-readonly
     */
    private $label;

    /**
     * @var string
     *
     * @psalm-readonly
     */
    private $destination;

    /**
     * @var string
     *
     * @psalm-readonly
     */
    private $title;

    public function __construct($label, $destination, $title) {
        $this->label = $label;
        $this->destination = $destination;
        $this->title = $title;
    }

    public function getLabel() {
        return $this->label;
    }

    public function getDestination() {
        return $this->destination;
    }

    public function getTitle() {
        return $this->title;
    }
}
