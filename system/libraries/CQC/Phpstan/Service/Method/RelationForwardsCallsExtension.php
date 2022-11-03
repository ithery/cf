<?php



use PHPStan\Type\Type;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeWithClassName;
use PHPStan\ShouldNotHappenException;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\Generic\TemplateMixedType;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\MissingMethodFromReflectionException;

final class CQC_Phpstan_Service_Method_RelationForwardsCallsExtension implements MethodsClassReflectionExtension {
    /**
     * @var BuilderHelper
     */
    private $builderHelper;

    /**
     * @var array<string, MethodReflection>
     */
    private $cache = [];

    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    /**
     * @var EloquentBuilderForwardsCallsExtension
     */
    private $modelQueryForwardsCallsExtension;

    public function __construct(CQC_Phpstan_Service_BuilderHelper $builderHelper, ReflectionProvider $reflectionProvider, CQC_Phpstan_Service_Method_ModelQueryForwardsCallsExtension $modelQueryForwardsCallsExtension) {
        $this->builderHelper = $builderHelper;
        $this->reflectionProvider = $reflectionProvider;
        $this->modelQueryForwardsCallsExtension = $modelQueryForwardsCallsExtension;
    }

    public function hasMethod(ClassReflection $classReflection, string $methodName): bool {
        if (array_key_exists($classReflection->getCacheKey() . '-' . $methodName, $this->cache)) {
            return true;
        }

        $methodReflection = $this->findMethod($classReflection, $methodName);

        if ($methodReflection !== null) {
            $this->cache[$classReflection->getCacheKey() . '-' . $methodName] = $methodReflection;

            return true;
        }

        return false;
    }

    public function getMethod(
        ClassReflection $classReflection,
        string $methodName
    ): MethodReflection {
        return $this->cache[$classReflection->getCacheKey() . '-' . $methodName];
    }

    /**
     * @throws MissingMethodFromReflectionException
     * @throws ShouldNotHappenException
     */
    private function findMethod(ClassReflection $classReflection, string $methodName): ?MethodReflection {
        if (!$classReflection->isSubclassOf(Relation::class)) {
            return null;
        }

        /** @var null|Type|TemplateMixedType $relatedModel */
        $relatedModel = $classReflection->getActiveTemplateTypeMap()->getType('TRelatedModel');

        if ($relatedModel === null) {
            return null;
        }

        if ($relatedModel instanceof TypeWithClassName) {
            $modelReflection = $relatedModel->getClassReflection();
        } else {
            $modelReflection = $this->reflectionProvider->getClass(CModel::class);
        }

        if ($modelReflection === null) {
            return null;
        }

        $builderName = $this->builderHelper->determineBuilderName($modelReflection->getName());

        $builderReflection = $this->reflectionProvider->getClass($builderName)->withTypes([$relatedModel]);

        if ($builderReflection->hasNativeMethod($methodName)) {
            $reflection = $builderReflection->getNativeMethod($methodName);
        } elseif ($this->modelQueryForwardsCallsExtension->hasMethod($builderReflection, $methodName)) {
            $reflection = $this->modelQueryForwardsCallsExtension->getMethod($builderReflection, $methodName);
        } else {
            return null;
        }

        $parametersAcceptor = ParametersAcceptorSelector::selectSingle($reflection->getVariants());
        $returnType = $parametersAcceptor->getReturnType();

        $types = [$relatedModel];

        // BelongsTo relation needs second generic type
        if ((new ObjectType(BelongsTo::class))->isSuperTypeOf(new ObjectType($classReflection->getName()))->yes()) {
            $childType = $classReflection->getActiveTemplateTypeMap()->getType('TChildModel');

            if ($childType !== null) {
                $types[] = $childType;
            }
        }

        if ((new ObjectType(Builder::class))->isSuperTypeOf($returnType)->yes()) {
            return new CQC_Phpstan_Reflection_ModelQueryMethodReflection(
                $methodName,
                $classReflection,
                $reflection,
                $parametersAcceptor->getParameters(),
                new GenericObjectType($classReflection->getName(), $types),
                $parametersAcceptor->isVariadic()
            );
        }

        return new CQC_Phpstan_Reflection_ModelQueryMethodReflection(
            $methodName,
            $classReflection,
            $reflection,
            $parametersAcceptor->getParameters(),
            $returnType,
            $parametersAcceptor->isVariadic()
        );
    }
}
