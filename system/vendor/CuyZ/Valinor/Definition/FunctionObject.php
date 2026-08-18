<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Definition;

/** @internal */
final class FunctionObject {
    public FunctionDefinition $definition;

    /** @var callable */
    public $callback;

    public function __construct(FunctionDefinition $definition, $callback) {
        $this->definition = $definition;
        $this->callback = $callback;
    }
}
