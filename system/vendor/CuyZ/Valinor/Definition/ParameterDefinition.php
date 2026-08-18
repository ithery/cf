<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Definition;

use CuyZ\Valinor\Type\Type;

/** @internal */
final class ParameterDefinition {
    public string $name;

    public string $signature;

    public Type $type;

    public Type $nativeType;

    public bool $isOptional;

    public bool $isVariadic;

    /**
     * @var mixed
     */
    public $defaultValue;

    public Attributes $attributes;

    public function __construct(
        /** @var non-empty-string */
        string $name,
        /** @var non-empty-string */
        string $signature,
        Type $type,
        Type $nativeType,
        bool $isOptional,
        bool $isVariadic,
        $defaultValue,
        Attributes $attributes
    ) {
        $this->name = $name;
        $this->signature = $signature;
        $this->type = $type;
        $this->nativeType = $nativeType;
        $this->isOptional = $isOptional;
        $this->isVariadic = $isVariadic;
        $this->defaultValue = $defaultValue;
        $this->attributes = $attributes;
    }
}
