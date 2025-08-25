<?php

namespace CuyZ\Valinor\Type;

/** @internal */
interface FixedType extends Type {
    /**
     * @return bool|string|int|float
     */
    public function value();
}
