<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Definition\Exception;

use LogicException;
use CuyZ\Valinor\Type\ObjectType;

/** @internal */
final class InvalidTypeAliasImportClass extends LogicException {
    public function __construct(ObjectType $type, string $className) {
        parent::__construct(
            "Cannot import a type alias from unknown class `$className` in class `{$type->className()}`.",
            1638535486
        );
    }
}
