<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Definition;

use CuyZ\Valinor\Type\ObjectType;

/** @internal */
final class ClassDefinition {
    public string $name;

    public ObjectType $type;

    public Attributes $attributes;

    public Properties $properties;

    public Methods $methods;

    public bool $isFinal;

    public bool $isAbstract;

    public function __construct(
        /** @var class-string */
        string $name,
        ObjectType $type,
        Attributes $attributes,
        Properties $properties,
        Methods $methods,
        bool $isFinal,
        bool $isAbstract
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->attributes = $attributes;
        $this->properties = $properties;
        $this->methods = $methods;
        $this->isFinal = $isFinal;
        $this->isAbstract = $isAbstract;
    }
}
