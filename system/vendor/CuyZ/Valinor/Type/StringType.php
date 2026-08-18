<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Type;

/** @internal */
interface StringType extends ScalarType {
    /**
     * @param mixed $value
     *
     * @return string
     */
    public function cast($value): string;
}
