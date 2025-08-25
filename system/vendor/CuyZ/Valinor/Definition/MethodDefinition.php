<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Definition;

use CuyZ\Valinor\Type\Type;

/** @internal */
final class MethodDefinition {
    /**
     * @var non-empty-string
     */
    public string $name;

    /**
     * @var non-empty-string
     */
    public string $signature;

    public Attributes $attributes;

    public Parameters $parameters;

    public bool $isStatic;

    public bool $isPublic;

    public Type $returnType;

    public function __construct(
        /** @var non-empty-string */
        string $name,
        /** @var non-empty-string */
        string $signature,
        Attributes $attributes,
        Parameters $parameters,
        bool $isStatic,
        bool $isPublic,
        Type $returnType
    ) {
        $this->name = $name;
        $this->signature = $signature;
        $this->attributes = $attributes;
        $this->parameters = $parameters;
        $this->isStatic = $isStatic;
        $this->isPublic = $isPublic;
        $this->returnType = $returnType;
    }
}
