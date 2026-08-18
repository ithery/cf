<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Normalizer\Transformer;

use Closure;
use CuyZ\Valinor\Cache\Cache;
use CuyZ\Valinor\Cache\CacheEntry;
use CuyZ\Valinor\Cache\TypeFilesWatcher;
use CuyZ\Valinor\Compiler\Compiler;
use CuyZ\Valinor\Compiler\Node;
use CuyZ\Valinor\Normalizer\Exception\TypeUnhandledByNormalizer;
use CuyZ\Valinor\Normalizer\Transformer\Compiler\TransformerDefinitionBuilder;
use CuyZ\Valinor\Normalizer\Transformer\Compiler\TransformerRootNode;
use CuyZ\Valinor\Type\CompositeTraversableType;
use CuyZ\Valinor\Type\Type;
use CuyZ\Valinor\Type\Types\ArrayKeyType;
use CuyZ\Valinor\Type\Types\ArrayType;
use CuyZ\Valinor\Type\Types\EnumType;
use CuyZ\Valinor\Type\Types\Factory\ValueTypeFactory;
use CuyZ\Valinor\Type\Types\IterableType;
use CuyZ\Valinor\Type\Types\NativeBooleanType;
use CuyZ\Valinor\Type\Types\NativeClassType;
use CuyZ\Valinor\Type\Types\NativeFloatType;
use CuyZ\Valinor\Type\Types\NativeIntegerType;
use CuyZ\Valinor\Type\Types\NativeStringType;
use CuyZ\Valinor\Type\Types\NonEmptyArrayType;
use CuyZ\Valinor\Type\Types\NonEmptyListType;
use CuyZ\Valinor\Type\Types\NullType;
use Generator;
use Iterator;
use IteratorAggregate;
use UnitEnum;

use function array_is_list;
use function is_array;
use function is_iterable;
use function is_object;
use function is_scalar;

/** @internal */
final class CompiledTransformer implements Transformer
{
    private TransformerDefinitionBuilder $definitionBuilder;
    private TypeFilesWatcher $typeFilesWatcher;
    /** @var Cache<Transformer> */
    private Cache $cache;
    /** @var list<callable> */
    private array $transformers;

    public function __construct(
        TransformerDefinitionBuilder $definitionBuilder,
        TypeFilesWatcher $typeFilesWatcher,
        /** @var Cache<Transformer> */
        Cache $cache,
        /** @var list<callable> */
        array $transformers
    ) {
        $this->definitionBuilder = $definitionBuilder;
        $this->typeFilesWatcher = $typeFilesWatcher;
        $this->cache = $cache;
        $this->transformers = $transformers;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function transform($value)
    {
        $type = $this->inferType($value, isSure: true);

        $key = "transformer-\0" . $type->toString();

        $transformer = $this->cache->get($key, $this->transformers, $this);

        if ($transformer) {
            return $transformer->transform($value);
        }

        $code = $this->compileFor($type);
        $filesToWatch = $this->typeFilesWatcher->for($type);

        $this->cache->set($key, new CacheEntry($code, $filesToWatch));

        $transformer = $this->cache->get($key, $this->transformers, $this);

        assert($transformer instanceof Transformer);

        return $transformer->transform($value);
    }

    private function compileFor(Type $type): string
    {
        $rootNode = new TransformerRootNode($this->definitionBuilder, $type);

        $node = Node::shortClosure($rootNode)
            ->witParameters(
                Node::parameterDeclaration('transformers', 'array'),
                Node::parameterDeclaration('delegate', Transformer::class),
            );

        return (new Compiler())->compile($node)->code();
    }

    private function inferType($value, bool $isSure = false): Type
    {
        // @infection-ignore-all (mutation from `true` to `false` is useless)
        if ($value instanceof UnitEnum) {
            return EnumType::native($value::class);
        } elseif (is_object($value) && ! $value instanceof Closure && ! $value instanceof Generator) {
            return $this->inferObjectType($value);
        } elseif (is_iterable($value)) {
            return $this->inferIterableType($value);
        } elseif (is_scalar($value) && $isSure) {
            return ValueTypeFactory::from($value);
        } elseif (is_string($value)) {
            return NativeStringType::get();
        } elseif (is_int($value)) {
            return NativeIntegerType::get();
        } elseif (is_float($value)) {
            return NativeFloatType::get();
        } elseif (is_bool($value)) {
            return NativeBooleanType::get();
        } elseif (is_null($value)) {
            return NullType::get();
        }

        throw new TypeUnhandledByNormalizer($value);
    }

    private function inferObjectType(object $value): NativeClassType
    {
        if (is_iterable($value)) {
            $iterableType = $this->inferIterableType($value);

            return new NativeClassType($value::class, ['SubType' => $iterableType->subType()]);
        }

        return new NativeClassType($value::class);
    }

    /**
     * This method contains a strongly opinionated rule: when normalizing an
     * iterable, we assume that the iterable has a high probability of
     * containing only one type of value, each iteration matching the type of
     * the first value.
     *
     * This is a trade-off between performance and accuracy: the first value
     * will always be normalized, so we can safely add its type to the compiling
     * process. For other values, if their types match the first one, that is a
     * big performance win; if they don't, the transformer will do its best to
     * find an optimized way of dealing with it or, by default, fall back to
     * the delegate transformer.
     *
     * @param iterable<mixed> $value
     */
    private function inferIterableType(iterable $value): CompositeTraversableType
    {
        if (is_array($value)) {
            if ($value === []) {
                return ArrayType::native();
            }

            $firstValueType = $this->inferType(reset($value));

            if (array_is_list($value)) {
                return new NonEmptyListType($firstValueType);
            }

            return new NonEmptyArrayType(ArrayKeyType::default(), $firstValueType);
        }

        if ($value instanceof IteratorAggregate) {
            $value = $value->getIterator();
        }

        if ($value instanceof Iterator && $value->valid()) {
            $firstValueType = $this->inferType($value->current());

            return new IterableType(ArrayKeyType::default(), $firstValueType);
        }

        return IterableType::native();
    }
}
