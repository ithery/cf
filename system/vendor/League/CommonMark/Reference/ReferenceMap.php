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

use League\CommonMark\Normalizer\TextNormalizer;

/**
 * A collection of references, indexed by label
 */
final class ReferenceMap implements ReferenceMapInterface {
    /**
     * @var TextNormalizer
     *
     * @psalm-readonly
     */
    private $normalizer;

    /**
     * @var array<string, ReferenceInterface>
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $references = [];

    public function __construct() {
        $this->normalizer = new TextNormalizer();
    }

    public function add(ReferenceInterface $reference) {
        // Normalize the key
        $key = $this->normalizer->normalize($reference->getLabel());
        // Store the reference
        $this->references[$key] = $reference;
    }

    public function contains($label) {
        $label = $this->normalizer->normalize($label);

        return isset($this->references[$label]);
    }

    public function get($label) {
        $label = $this->normalizer->normalize($label);

        if (!isset($this->references[$label])) {
            return null;
        }

        return $this->references[$label];
    }

    /**
     * @return \Traversable<string, ReferenceInterface>
     */
    public function getIterator() {
        foreach ($this->references as $normalizedLabel => $reference) {
            yield $normalizedLabel => $reference;
        }
    }

    public function count() {
        return \count($this->references);
    }
}
