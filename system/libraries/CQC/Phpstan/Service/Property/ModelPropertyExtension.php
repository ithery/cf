<?php

use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\ShouldNotHappenException;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\PropertiesClassReflectionExtension;

/**
 * @internal
 */
final class CQC_Phpstan_Service_Property_ModelPropertyExtension implements PropertiesClassReflectionExtension {
    /**
     * @var array<string, SchemaTable>
     */
    private $tables = [];

    /**
     * @var string
     */
    private $dateClass;

    /**
     * @var TypeStringResolver
     */
    private $stringResolver;

    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    public function __construct(TypeStringResolver $stringResolver, ReflectionProvider $reflectionProvider) {
        $this->stringResolver = $stringResolver;
        $this->reflectionProvider = $reflectionProvider;
    }

    public function hasProperty(ClassReflection $classReflection, string $propertyName): bool {
        if (!$classReflection->isSubclassOf(CModel::class)) {
            return false;
        }

        if ($classReflection->isAbstract()) {
            return false;
        }

        if ($this->hasAttribute($classReflection, $propertyName)) {
            return false;
        }

        if (CQC_Phpstan_Reflection_ReflectionHelper::hasPropertyTag($classReflection, $propertyName)) {
            return false;
        }
        if ($propertyName == 'pivot') {
            //TODO: check for belongsToMany relation
            return true;
        }

        return false;
    }

    public function getProperty(
        ClassReflection $classReflection,
        string $propertyName
    ): PropertyReflection {
        $modelName = $classReflection->getNativeReflection()->getName();

        if ($propertyName == 'pivot') {
            return new CQC_Phpstan_Service_Property_PivotProperty(
                $classReflection,
            );
        }

        return new CQC_Phpstan_Service_Property_ModelProperty(
            $classReflection,
            new StringType(),
            new StringType()
        );
    }

    private function getDateClass(): string {
        if (!$this->dateClass) {
            $this->dateClass = '\CCarbon|\Carbon\Carbon';
        }

        return $this->dateClass;
    }

    /**
     * @param CModel $modelInstance
     *
     * @return string[]
     *
     * @phpstan-return array<int, string>
     */
    private function getModelDateColumns(CModel $modelInstance): array {
        $dateColumns = $modelInstance->getDates();

        if (method_exists($modelInstance, 'getDeletedAtColumn')) {
            $dateColumns[] = $modelInstance->getDeletedAtColumn();
        }

        return $dateColumns;
    }

    private function hasAttribute(ClassReflection $classReflection, string $propertyName): bool {
        if ($classReflection->hasNativeMethod('get' . cstr::studly($propertyName) . 'Attribute')) {
            return true;
        }

        $camelCase = cstr::camel($propertyName);

        if ($classReflection->hasNativeMethod($camelCase)) {
            $methodReflection = $classReflection->getNativeMethod($camelCase);

            if ($methodReflection->isPublic() || $methodReflection->isPrivate()) {
                return false;
            }

            $returnType = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();

            if (!(new ObjectType(CModel_Casts_Attribute::class))->isSuperTypeOf($returnType)->yes()) {
                return false;
            }

            return true;
        }

        return false;
    }
}
