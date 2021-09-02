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

declare(strict_types=1);

namespace League\CommonMark\Extension\Attributes\Node;

use League\CommonMark\Node\Block\AbstractBlock;

final class Attributes extends AbstractBlock {
    const TARGET_PARENT = 0;

    const TARGET_PREVIOUS = 1;

    const TARGET_NEXT = 2;

    /**
     * @var array<string, mixed>
     */
    private $attributes;

    /**
     * @var int
     */
    private $target = self::TARGET_NEXT;

    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(array $attributes) {
        parent::__construct();

        $this->attributes = $attributes;
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

    /**
     * @return int
     */
    public function getTarget() {
        return $this->target;
    }

    /**
     * @param int $target
     *
     * @return void
     */
    public function setTarget($target) {
        $this->target = $target;
    }
}
