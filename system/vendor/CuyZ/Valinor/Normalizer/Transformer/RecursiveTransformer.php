<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Normalizer\Transformer;

use Closure;
use WeakMap;
use stdClass;
use UnitEnum;
use BackedEnum;
use DateTimeZone;
use DateTimeInterface;
use function is_array;
use function array_map;
use function is_object;
use function is_iterable;
use function get_object_vars;
use CuyZ\Valinor\Type\Types\EnumType;
use CuyZ\Valinor\Definition\Attributes;
use CuyZ\Valinor\Type\Types\NativeClassType;

use CuyZ\Valinor\Definition\AttributeDefinition;
use CuyZ\Valinor\Normalizer\Exception\TypeUnhandledByNormalizer;
use CuyZ\Valinor\Definition\Repository\ClassDefinitionRepository;
use CuyZ\Valinor\Definition\Repository\FunctionDefinitionRepository;
use CuyZ\Valinor\Normalizer\Exception\CircularReferenceFoundDuringNormalization;

/**  @internal */
final class RecursiveTransformer implements Transformer {
    private ClassDefinitionRepository $classDefinitionRepository;

    private FunctionDefinitionRepository $functionDefinitionRepository;

    private TransformerContainer $transformerContainer;

    public function __construct(
        ClassDefinitionRepository $classDefinitionRepository,
        FunctionDefinitionRepository $functionDefinitionRepository,
        TransformerContainer $transformerContainer
    ) {
        $this->classDefinitionRepository = $classDefinitionRepository;
        $this->functionDefinitionRepository = $functionDefinitionRepository;
        $this->transformerContainer = $transformerContainer;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function transform($value) {
        return $this->doTransform($value, new WeakMap()); // @phpstan-ignore-line
    }

    /**
     * @param WeakMap<object, object>   $references
     * @param list<AttributeDefinition> $attributes
     * @param mixed                     $value
     *
     * @return mixed
     */
    private function doTransform($value, WeakMap $references, array $attributes = []) {
        if (is_object($value)) {
            if (isset($references[$value])) {
                throw new CircularReferenceFoundDuringNormalization($value);
            }

            $references = clone $references;
            $references[$value] = $value;

            $type = $value instanceof UnitEnum
                ? EnumType::native(\get_class($value))
                : new NativeClassType(\get_class($value));

            $classAttributes = $this->classDefinitionRepository->for($type)->attributes->toArray();

            $attributes = [...$attributes, ...$classAttributes];
        }

        if (!$this->transformerContainer->hasTransformers() && $attributes === []) {
            return $this->defaultTransformer($value, $references);
        }

        // if ($attributes !== []) {
        //     $attributes = (new Attributes(...$attributes))
        //         ->filter(TransformerContainer::filterTransformerAttributes(...))
        //         ->toArray();
        // }
        if ($attributes !== []) {
            $attributes = (new Attributes(...$attributes))
                ->filter(function ($attribute) {
                    return TransformerContainer::filterTransformerAttributes($attribute);
                })
                ->toArray();
        }
        // $transformers = [
        //     // First chunk of transformers to be used: attributes, coming from
        //     // class or property.
        //     ...array_map(
        //         fn (AttributeDefinition $attribute) => $attribute->instantiate()->normalize(...), // @phpstan-ignore-line / We know the method exists
        //         $attributes,
        //     ),
        //     // Second chunk of transformers to be used: registered transformers.
        //     ...$this->transformerContainer->transformers(),
        //     // Last one: default transformer.
        //     fn (mixed $value) => $this->defaultTransformer($value, $references),
        // ];
        $transformers = array_merge(
            // First chunk of transformers to be used: attributes, coming from class or property.
            array_map(
                function (AttributeDefinition $attribute) {
                    return [$attribute->instantiate(), 'normalize']; // ganti first-class callable
                },
                $attributes
            ),
            // Second chunk of transformers to be used: registered transformers.
            $this->transformerContainer->transformers(),
            // Last one: default transformer.
            [
                function ($value) use ($references) {
                    return $this->defaultTransformer($value, $references);
                }
            ]
        );

        return call_user_func($this->nextTransformer($transformers, $value));
    }

    /**
     * @param non-empty-list<callable> $transformers
     * @param mixed                    $value
     */
    private function nextTransformer(array $transformers, $value): callable {
        $transformer = array_shift($transformers);

        if ($transformers === []) {
            return fn () => $transformer($value);
        }

        $function = $this->functionDefinitionRepository->for($transformer);

        if (!$function->parameters->at(0)->type->accepts($value)) {
            return $this->nextTransformer($transformers, $value);
        }

        return fn () => $transformer($value, fn () => call_user_func($this->nextTransformer($transformers, $value)));
    }

    /**
     * @param WeakMap<object, object> $references
     * @param mixed                   $value
     *
     * @return null|iterable<mixed>|scalar
     */
    private function defaultTransformer($value, WeakMap $references) {
        if ($value === null) {
            return null;
        }

        if (is_scalar($value)) {
            return $value;
        }

        if (is_iterable($value)) {
            if (is_array($value)) {
                return array_map(
                    fn ($value) => $this->doTransform($value, $references),
                    $value
                );
            }

            return (function () use ($value, $references) {
                foreach ($value as $key => $item) {
                    yield $key => $this->doTransform($item, $references);
                }
            })();
        }

        if (is_object($value) && !$value instanceof Closure) {
            if ($value instanceof UnitEnum) {
                return $value instanceof BackedEnum ? $value->value : $value->name;
            }

            if ($value instanceof DateTimeInterface) {
                return $value->format('Y-m-d\\TH:i:s.uP'); // RFC 3339
            }

            if ($value instanceof DateTimeZone) {
                return $value->getName();
            }

            if (\get_class($value) === stdClass::class) {
                $result = (array) $value;

                if ($result === []) {
                    return EmptyObject::get();
                }

                return array_map(
                    fn ($value) => $this->doTransform($value, $references),
                    $result,
                );
            }

            $values = (fn () => get_object_vars($this))->call($value);

            $transformed = [];

            $class = $this->classDefinitionRepository->for(new NativeClassType(\get_class($value)));

            // foreach ($values as $key => $subValue) {
            //     $property = $class->properties->get($key);

            //     $keyTransformersAttributes = $property->attributes->filter(TransformerContainer::filterKeyTransformerAttributes(...));

            //     foreach ($keyTransformersAttributes as $attribute) {
            //         $method = $attribute->class->methods->get('normalizeKey');

            //         if ($method->parameters->count() === 0 || $method->parameters->at(0)->type->accepts($key)) {
            //             $key = $attribute->instantiate()->normalizeKey($key); // @phpstan-ignore-line / We know the method exists
            //         }
            //     }

            //     $transformed[$key] = $this->doTransform($subValue, $references, $property->attributes->toArray());
            // }
            foreach ($values as $key => $subValue) {
                $property = $class->properties->get($key);

                // Ganti first-class callable ke array callable biasa
                $keyTransformersAttributes = $property->attributes->filter(function (AttributeDefinition $attribute) {
                    return TransformerContainer::filterKeyTransformerAttributes($attribute);
                });

                foreach ($keyTransformersAttributes as $attribute) {
                    $method = $attribute->class->methods->get('normalizeKey');

                    if ($method->parameters->count() === 0 || $method->parameters->at(0)->type->accepts($key)) {
                        $key = $attribute->instantiate()->normalizeKey($key); // @phpstan-ignore-line
                    }
                }

                $transformed[$key] = $this->doTransform(
                    $subValue,
                    $references,
                    $property->attributes->toArray()
                );
            }

            return $transformed;
        }

        throw new TypeUnhandledByNormalizer($value);
    }
}
