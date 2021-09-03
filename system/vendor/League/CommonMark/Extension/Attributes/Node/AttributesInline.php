<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) 2015 Martin Hasoň <martin.hason@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\Attributes\Node;

use League\CommonMark\Node\Inline\AbstractInline;

final class AttributesInline extends AbstractInline {
    /**
     * @var array<string, mixed>
     */
    public $attributes;

    /**
     * @var bool
     */
    public $block;

    /**
     * @param array<string, mixed> $attributes
     * @param mixed                $block
     */
    public function __construct(array $attributes, $block) {
        parent::__construct();

        $this->attributes = $attributes;
        $this->block = $block;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function setAttributes(array $attributes) {
        $this->attributes = $attributes;
    }

    public function isBlock() {
        return $this->block;
    }
}
