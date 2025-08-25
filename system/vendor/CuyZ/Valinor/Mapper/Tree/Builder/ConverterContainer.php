<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Mapper\Tree\Builder;

use CuyZ\Valinor\Definition\AttributeDefinition;
use CuyZ\Valinor\Definition\FunctionDefinition;
use CuyZ\Valinor\Definition\MethodDefinition;
use CuyZ\Valinor\Definition\Repository\FunctionDefinitionRepository;
use CuyZ\Valinor\Mapper\Tree\Exception\ConverterHasInvalidCallableParameter;
use CuyZ\Valinor\Mapper\Tree\Exception\ConverterHasNoParameter;
use CuyZ\Valinor\Mapper\Tree\Exception\ConverterHasTooManyParameters;
use CuyZ\Valinor\Type\Types\CallableType;

/** @internal */
final class ConverterContainer
{
    private bool $convertersCallablesWereChecked = false;

    private FunctionDefinitionRepository $functionDefinitionRepository;

    private array $converters;
    public function __construct(
        FunctionDefinitionRepository $functionDefinitionRepository,
        /** @var list<callable> */
        array $converters
    ) {
        $this->functionDefinitionRepository = $functionDefinitionRepository;
        $this->converters = $converters;
    }

    /**
     * @return list<callable>
     */
    public function converters(): array
    {
        if (! $this->convertersCallablesWereChecked) {
            $this->convertersCallablesWereChecked = true;

            foreach ($this->converters as $transformer) {
                $function = $this->functionDefinitionRepository->for($transformer);

                self::checkConverter($function);
            }
        }

        return $this->converters;
    }

    public static function filterConverterAttributes(AttributeDefinition $attribute): bool
    {
        return $attribute->class->methods->has('map')
            && self::checkConverter($attribute->class->methods->get('map'));
    }

    /**
     * @param MethodDefinition|FunctionDefinition $method
     * @return boolean
     */
    private static function checkConverter($method): bool
    {
        $parameters = $method->parameters;

        if ($parameters->count() === 0) {
            throw new ConverterHasNoParameter($method);
        }

        if ($parameters->count() > 2) {
            throw new ConverterHasTooManyParameters($method);
        }

        if ($parameters->count() > 1 && ! $parameters->at(1)->nativeType instanceof CallableType) {
            throw new ConverterHasInvalidCallableParameter($method, $parameters->at(1)->nativeType);
        }

        return true;
    }
}
