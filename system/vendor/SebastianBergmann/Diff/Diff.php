<?php

declare(strict_types=1);
/*
 * This file is part of sebastian/diff.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\Diff;

final class Diff {
    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @var Chunk[]
     */
    private $chunks;

    /**
     * @param Chunk[] $chunks
     */
    public function __construct(string $from, string $to, array $chunks = []) {
        $this->from = $from;
        $this->to = $to;
        $this->chunks = $chunks;
    }

    public function getFrom() {
        return $this->from;
    }

    public function getTo() {
        return $this->to;
    }

    /**
     * @return Chunk[]
     */
    public function getChunks() {
        return $this->chunks;
    }

    /**
     * @param Chunk[] $chunks
     */
    public function setChunks(array $chunks) {
        $this->chunks = $chunks;
    }
}
