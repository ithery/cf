<?php

use PHPStan\Type\Type;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ArrayType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\TypeCombinator;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;

/**
 * @internal
 */
final class CQC_Phpstan_Service_ReturnType_RelationFindExtension implements DynamicMethodReturnTypeExtension {
    /**
     * @var CQC_Phpstan_Service_BuilderHelper
     */
    private $builderHelper;

    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    public function __construct(ReflectionProvider $reflectionProvider, CQC_Phpstan_Service_BuilderHelper $builderHelper) {
        $this->builderHelper = $builderHelper;
        $this->reflectionProvider = $reflectionProvider;
    }

    /**
     * @inheritdoc
     */
    public function getClass(): string {
        return CModel_Relation::class;
    }

    /**
     * @inheritdoc
     */
    public function isMethodSupported(MethodReflection $methodReflection): bool {
        if (!cstr::startsWith($methodReflection->getName(), 'find')) {
            return false;
        }

        $modelType = $methodReflection->getDeclaringClass()->getActiveTemplateTypeMap()->getType('TRelatedModel');

        if (!$modelType instanceof ObjectType) {
            return false;
        }

        return $methodReflection->getDeclaringClass()->hasNativeMethod($methodReflection->getName())
            || $this->reflectionProvider->getClass(CModel_Query::class)->hasNativeMethod($methodReflection->getName())
            || $this->reflectionProvider->getClass(CDatabase_Query_Builder::class)->hasNativeMethod($methodReflection->getName());
    }

    /**
     * @inheritdoc
     */
    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        /** @var ObjectType $modelType */
        $modelType = $methodReflection->getDeclaringClass()->getActiveTemplateTypeMap()->getType('TRelatedModel');

        $argType = $scope->getType($methodCall->getArgs()[0]->value);

        $returnType = $methodReflection->getVariants()[0]->getReturnType();

        if (in_array(Collection::class, $returnType->getReferencedClasses(), true)) {
            if ($argType->isIterable()->yes()) {
                $collectionClassName = $this->builderHelper->determineCollectionClassName($modelType->getClassname());

                return new GenericObjectType($collectionClassName, [new IntegerType(), $modelType]);
            }

            $returnType = TypeCombinator::remove($returnType, new ObjectType(Collection::class));

            return TypeCombinator::remove($returnType, new ArrayType(new MixedType(), $modelType));
        }

        return $returnType;
    }
}
