<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Normalizer;

use CuyZ\Valinor\Normalizer\Transformer\EmptyObject;
use CuyZ\Valinor\Normalizer\Transformer\Transformer;

/**
 * @api
 *
 * @implements Normalizer<array<mixed>|scalar|null>
 */
final class ArrayNormalizer implements Normalizer {
    private Transformer $transformer;

    /**
     * @internal
     */
    public function __construct(
        Transformer $transformer
    ) {
        $this->transformer = $transformer;
    }

    /**
     * @pure
     *
     * @param mixed $value
     */
    public function normalize($value) {
        /** @var null|array<mixed>|scalar */
        return $this->format(
            $this->transformer->transform($value),
        );
    }

    private function format($value) {
        if (is_iterable($value)) {
            if (!is_array($value)) {
                $value = iterator_to_array($value);
            }

            // $value = array_map($this->format(...), $value);
            $value = array_map([$this, 'format'], $value);
        } elseif ($value instanceof EmptyObject) {
            return [];
        }

        return $value;
    }
}
