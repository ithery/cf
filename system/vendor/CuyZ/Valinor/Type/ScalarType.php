<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Type;

use CuyZ\Valinor\Mapper\Tree\Message\ErrorMessage;

/** @internal */
interface ScalarType extends Type {
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function canCast($value): bool;

    /**
     * @param mixed $value
     *
     * @return bool|string|int|float
     */
    public function cast($value);

    public function errorMessage(): ErrorMessage;
}
