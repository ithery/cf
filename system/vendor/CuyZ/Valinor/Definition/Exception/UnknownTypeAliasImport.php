<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Definition\Exception;

use LogicException;
use CuyZ\Valinor\Type\ObjectType;

/** @internal */
final class UnknownTypeAliasImport extends LogicException {
    /**
     * @param class-string $importClassName
     */
    public function __construct(ObjectType $type, string $importClassName, string $alias) {
        parent::__construct(
            "Type alias `$alias` imported in `{$type->className()}` could not be found in `$importClassName`",
            1638535757
        );
    }
}
