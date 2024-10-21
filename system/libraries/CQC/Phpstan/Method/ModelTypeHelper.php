<?php

use PHPStan\Type\Type;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StaticType;
use PHPStan\Type\TypeTraverser;
use PHPStan\Type\TypeWithClassName;
use PHPStan\Type\ObjectWithoutClassType;

final class CQC_Phpstan_Method_ModelTypeHelper {
    public static function replaceStaticTypeWithModel(Type $type, string $modelClass): Type {
        return TypeTraverser::map($type, static function (Type $type, callable $traverse) use ($modelClass): Type {
            if ($type instanceof ObjectWithoutClassType || $type instanceof StaticType) {
                return new ObjectType($modelClass);
            }

            if ($type instanceof TypeWithClassName && $type->getClassName() === CModel::class) {
                return new ObjectType($modelClass);
            }

            return $traverse($type);
        });
    }
}
