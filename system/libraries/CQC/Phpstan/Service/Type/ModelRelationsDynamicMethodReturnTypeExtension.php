<?php

use PHPStan\Type\Type;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\ShouldNotHappenException;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicMethodReturnTypeExtension;

class CQC_Phpstan_Service_Type_ModelRelationsDynamicMethodReturnTypeExtension implements DynamicMethodReturnTypeExtension {
    use CQC_Phpstan_Concern_HasContainer;

    /**
     * @var CQC_Phpstan_Service_RelationParserHelper
     */
    private $relationParserHelper;

    public function __construct(CQC_Phpstan_Service_RelationParserHelper $relationParserHelper) {
        $this->relationParserHelper = $relationParserHelper;
    }

    public function getClass(): string {
        return CModel::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool {
        $variants = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants());

        $returnType = $variants->getReturnType();

        if (!$returnType instanceof ObjectType) {
            return false;
        }

        if (!(new ObjectType(CModel_Relation::class))->isSuperTypeOf($returnType)->yes()) {
            return false;
        }

        if (!$methodReflection->getDeclaringClass()->hasNativeMethod($methodReflection->getName())) {
            return false;
        }

        if (count($variants->getParameters()) !== 0) {
            return false;
        }

        if (in_array($methodReflection->getName(), [
            'hasOne', 'hasOneThrough', 'morphOne',
            'belongsTo', 'morphTo',
            'hasMany', 'hasManyThrough', 'morphMany',
            'belongsToMany', 'morphToMany', 'morphedByMany',
        ], true)
        ) {
            return false;
        }

        $relatedModel = $this
            ->relationParserHelper
            ->findRelatedModelInRelationMethod($methodReflection);

        return $relatedModel !== null;
    }

    /**
     * @param MethodReflection $methodReflection
     * @param MethodCall       $methodCall
     * @param Scope            $scope
     *
     * @throws ShouldNotHappenException
     *
     * @return Type
     */
    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        /** @var ObjectType $returnType */
        $returnType = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();

        /** @var string $relatedModelClassName */
        $relatedModelClassName = $this
            ->relationParserHelper
            ->findRelatedModelInRelationMethod($methodReflection);

        $classReflection = $methodReflection->getDeclaringClass();

        if ($returnType->isInstanceOf(CModel_Relation_BelongsTo::class)->yes()) {
            return new GenericObjectType($returnType->getClassName(), [
                new ObjectType($relatedModelClassName),
                new ObjectType($classReflection->getName()),
            ]);
        }

        return new GenericObjectType($returnType->getClassName(), [new ObjectType($relatedModelClassName)]);
    }
}
