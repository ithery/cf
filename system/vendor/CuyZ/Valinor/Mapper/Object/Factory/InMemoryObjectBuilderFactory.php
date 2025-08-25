<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Mapper\Object\Factory;

use CuyZ\Valinor\Definition\ClassDefinition;
use CuyZ\Valinor\Mapper\Object\ObjectBuilder;

/** @internal */
final class InMemoryObjectBuilderFactory implements ObjectBuilderFactory {
    /**
     * @var array<string, non-empty-list<ObjectBuilder>>
     */
    private array $builders = [];

    private ObjectBuilderFactory $delegate;

    public function __construct(
        ObjectBuilderFactory $delegate
    ) {
        $this->delegate = $delegate;
    }

    public function for(ClassDefinition $class): array {
        return $this->builders[$class->type->toString()] ??= $this->delegate->for($class);
    }
}
