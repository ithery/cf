<?php

use PHPStan\Type\Type;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\Generic\GenericObjectType;

final class CQC_Phpstan_Service_Rule_ModelRuleHelper {
    public function findModelReflectionFromType(Type $type): ?ClassReflection {
        if (!(new ObjectType(CDatabase_Query_Builder::class))->isSuperTypeOf($type)->yes()
            && !(new ObjectType(CModel_Query::class))->isSuperTypeOf($type)->yes()
            && !(new ObjectType(CModel_Relation::class))->isSuperTypeOf($type)->yes()
            && !(new ObjectType(CModel::class))->isSuperTypeOf($type)->yes()
        ) {
            return null;
        }

        // We expect it to be generic builder or relation class with model type inside
        if ((!$type instanceof GenericObjectType) && (new ObjectType(CModel::class))->isSuperTypeOf($type)->no()) {
            return null;
        }

        if ($type instanceof GenericObjectType) {
            $modelType = $type->getTypes()[0];
        } else {
            $modelType = $type;
        }

        $modelType = TypeCombinator::removeNull($modelType);

        if (!$modelType instanceof ObjectType) {
            return null;
        }

        if ($modelType->getClassName() === CModel::class) {
            return null;
        }

        $modelReflection = $modelType->getClassReflection();

        if ($modelReflection === null) {
            return null;
        }

        if ($modelReflection->isAbstract()) {
            return null;
        }

        return $modelReflection;
    }
}
