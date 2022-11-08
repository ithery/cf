<?php

use PHPStan\Type\Type;
use PHPStan\Type\MixedType;
use PHPStan\Reflection\PassedByReference;
use PHPStan\Reflection\ParameterReflection;

class CQC_Phpstan_Reflection_DynamicWhereParameterReflection implements ParameterReflection {
    public function getName(): string {
        return 'dynamic-where-parameter';
    }

    public function isOptional(): bool {
        return true;
    }

    public function getType(): Type {
        return new MixedType();
    }

    public function passedByReference(): PassedByReference {
        return PassedByReference::createNo();
    }

    public function isVariadic(): bool {
        return false;
    }

    public function getDefaultValue(): ?Type {
        return null;
    }
}
