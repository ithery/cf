<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Normalizer\Transformer;

/** @internal */
interface Transformer
{
    /**
     * @param mixed $value
     * @return mixed
     */
    public function transform($value);
}
