<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Normalizer;

use CuyZ\Valinor\Normalizer\Formatter\JsonFormatter;
use CuyZ\Valinor\Normalizer\Transformer\Transformer;

/**
 * @api
 *
 * @implements Normalizer<resource>
 */
final class StreamNormalizer implements Normalizer
{
    private Transformer $transformer;
    private JsonFormatter $formatter;
    /**
     * @internal
     */
    public function __construct(
        Transformer $transformer,
        JsonFormatter $formatter
    ) {
        $this->transformer = $transformer;
        $this->formatter = $formatter;
    }

    /**
     * @pure
     */
    public function normalize($value)
    {
        $result = $this->transformer->transform($value);

        return $this->formatter->format($result);
    }
}
