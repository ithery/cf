<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Definition;

/** @internal */
final class AttributeDefinition
{
    public ClassDefinition $class;

    public array $arguments;
    public function __construct(
        ClassDefinition $class,
        /** @var list<mixed> */
        array $arguments
    ) {
        $this->class = $class;
        $this->arguments = $arguments;
    }

    public function instantiate(): object {
        return new ($this->class->type->className())(...$this->arguments);
    }
}
