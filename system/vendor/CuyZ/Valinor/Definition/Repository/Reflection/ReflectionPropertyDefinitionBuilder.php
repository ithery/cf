<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Definition\Repository\Reflection;

use CuyZ\Valinor\Definition\Attributes;
use CuyZ\Valinor\Definition\PropertyDefinition;
use CuyZ\Valinor\Definition\Repository\AttributesRepository;
use CuyZ\Valinor\Definition\Repository\Reflection\TypeResolver\PropertyTypeResolver;
use CuyZ\Valinor\Definition\Repository\Reflection\TypeResolver\ReflectionTypeResolver;
use CuyZ\Valinor\Type\Type;
use CuyZ\Valinor\Type\Types\NullType;
use CuyZ\Valinor\Type\Types\UnresolvableType;
use ReflectionProperty;

/** @internal */
final class ReflectionPropertyDefinitionBuilder
{
    private AttributesRepository $attributesRepository;
    public function __construct(AttributesRepository $attributesRepository) {
        $this->attributesRepository = $attributesRepository;
    }

    public function for(ReflectionProperty $reflection, ReflectionTypeResolver $typeResolver): PropertyDefinition
    {
        $propertyTypeResolver = new PropertyTypeResolver($typeResolver);

        /** @var non-empty-string $name */
        $name = $reflection->name;
        $signature = $reflection->getDeclaringClass()->name . '::$' . $reflection->name;
        $type = $propertyTypeResolver->resolveTypeFor($reflection);
        $nativeType = $propertyTypeResolver->resolveNativeTypeFor($reflection);
        $hasDefaultValue = $this->hasDefaultValue($reflection, $type);
        // Backward compat getDefaultValue()
        if (method_exists($reflection, 'getDefaultValue')) {
            // PHP 8+
            $defaultValue = $reflection->getDefaultValue();
        } else {
            // PHP 7.4 fallback
            $defaults = $reflection->getDeclaringClass()->getDefaultProperties();
            $defaultValue = array_key_exists($reflection->name, $defaults) ? $defaults[$reflection->name] : null;
        }
        // $defaultValue = $reflection->getDefaultValue();
        $isPublic = $reflection->isPublic();

        if ($type instanceof UnresolvableType) {
            $type = $type->forProperty($signature);
        } elseif (! $type->matches($nativeType)) {
            $type = UnresolvableType::forNonMatchingPropertyTypes($signature, $nativeType, $type);
        } elseif ($hasDefaultValue && ! $type->accepts($defaultValue)) {
            $type = UnresolvableType::forInvalidPropertyDefaultValue($signature, $type, $defaultValue);
        }

        return new PropertyDefinition(
            $name,
            $signature,
            $type,
            $nativeType,
            $hasDefaultValue,
            $defaultValue,
            $isPublic,
            new Attributes(...$this->attributesRepository->for($reflection)),
        );
    }

    private function hasDefaultValue(ReflectionProperty $reflection, Type $type): bool
    {
        if ($reflection->hasType()) {
            if (method_exists($reflection, 'hasDefaultValue')) {
                // PHP 8+
                return $reflection->hasDefaultValue();
            }
             // PHP 7.4 fallback
            $defaults = $reflection->getDeclaringClass()->getDefaultProperties();
            return array_key_exists($reflection->name, $defaults);
        }

        return $reflection->getDeclaringClass()->getDefaultProperties()[$reflection->name] !== null
            || NullType::get()->matches($type);
    }
}
