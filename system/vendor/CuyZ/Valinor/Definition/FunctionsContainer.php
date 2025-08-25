<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Definition;

use Countable;
use Traversable;
use function count;
use IteratorAggregate;

use function array_keys;
use function iterator_to_array;
use CuyZ\Valinor\Definition\Repository\FunctionDefinitionRepository;

/**
 * @internal
 *
 * @implements IteratorAggregate<string|int, FunctionObject>
 */
final class FunctionsContainer implements IteratorAggregate, Countable {
    /**
     * @var array<FunctionObject>
     */
    private array $functions = [];

    private FunctionDefinitionRepository $functionDefinitionRepository;

    private array $callables;

    public function __construct(
        FunctionDefinitionRepository $functionDefinitionRepository,
        /** @var array<callable> */
        array $callables
    ) {
        $this->functionDefinitionRepository = $functionDefinitionRepository;
        $this->callables = $callables;
    }

    /**
     * @param string|int $key
     *
     * @return bool
     */
    public function has($key): bool {
        return isset($this->callables[$key]);
    }

    /**
     * @param string|int $key
     *
     * @return FunctionObject
     */
    public function get($key): FunctionObject {
        return $this->function($key);
    }

    /**
     * @return array<FunctionObject>
     */
    public function toArray(): array {
        return iterator_to_array($this);
    }

    public function getIterator(): Traversable {
        foreach (array_keys($this->callables) as $key) {
            yield $key => $this->function($key);
        }
    }

    /**
     * @param string|int $key
     *
     * @return FunctionObject
     */
    private function function($key): FunctionObject {
        return $this->functions[$key] ??= new FunctionObject(
            $this->functionDefinitionRepository->for($this->callables[$key]),
            $this->callables[$key]
        );
    }

    public function count(): int {
        return count($this->callables);
    }
}
