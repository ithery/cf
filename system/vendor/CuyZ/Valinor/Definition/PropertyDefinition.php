<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Definition;

use CuyZ\Valinor\Type\Type;

/** @internal */
final class PropertyDefinition
{
    public string $name;

    public string $signature;

    public Type $type;

    public Type $nativeType;

    public bool $hasDefaultValue;

    /**
     * @var mixed
     */
    public $defaultValue;

    public bool $isPublic;

    public Attributes $attributes;
    public function __construct(
        /** @var non-empty-string */
        string $name,
        /** @var non-empty-string */
        string $signature,
        Type $type,
        Type $nativeType,
        bool $hasDefaultValue,
        $defaultValue,
        bool $isPublic,
        Attributes $attributes
    ) {
        $this->name = $name;
        $this->signature = $signature;
        $this->type = $type;
        $this->nativeType = $nativeType;
        $this->hasDefaultValue = $hasDefaultValue;
        $this->defaultValue = $defaultValue;
        $this->isPublic = $isPublic;
        $this->attributes = $attributes;
    }
}
