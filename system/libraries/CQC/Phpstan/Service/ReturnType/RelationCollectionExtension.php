<?php

use PHPStan\Type\Type;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use PHPStan\Type\IntegerType;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicMethodReturnTypeExtension;

/**
 * @internal
 */
final class CQC_Phpstan_Service_ReturnType_RelationCollectionExtension implements DynamicMethodReturnTypeExtension {
    /**
     * @var CQC_Phpstan_Service_BuilderHelper
     */
    private $builderHelper;

    public function __construct(CQC_Phpstan_Service_BuilderHelper $builderHelper) {
        $this->builderHelper = $builderHelper;
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
        if (cstr::startsWith($methodReflection->getName(), 'find')) {
            return false;
        }

        $modelType = $methodReflection->getDeclaringClass()->getActiveTemplateTypeMap()->getType('TRelatedModel');

        if (!$modelType instanceof ObjectType) {
            return false;
        }

        $returnType = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();

        if (!in_array(CModel_Collection::class, $returnType->getReferencedClasses(), true)) {
            return false;
        }

        return $methodReflection->getDeclaringClass()->hasNativeMethod($methodReflection->getName());
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

        $returnType = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();

        if (in_array(CModel_Collection::class, $returnType->getReferencedClasses(), true)) {
            $collectionClassName = $this->builderHelper->determineCollectionClassName($modelType->getClassname());

            return new GenericObjectType($collectionClassName, [new IntegerType(), $modelType]);
        }

        return $returnType;
    }
}
