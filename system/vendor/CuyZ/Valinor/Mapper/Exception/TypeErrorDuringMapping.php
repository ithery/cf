<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Mapper\Exception;

use LogicException;
use CuyZ\Valinor\Type\Type;
use CuyZ\Valinor\Mapper\Tree\Exception\UnresolvableShellType;

/** @internal */
final class TypeErrorDuringMapping extends LogicException {
    public function __construct(Type $type, UnresolvableShellType $exception) {
        parent::__construct(
            "Error while trying to map to `{$type->toString()}`: {$exception->getMessage()}",
            1711526329,
            $exception,
        );
    }
}
