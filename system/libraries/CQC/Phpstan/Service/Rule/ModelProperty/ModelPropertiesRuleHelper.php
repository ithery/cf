<?php

use PhpParser\Node;
use PHPStan\Type\Type;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ArrayType;
use PHPStan\Type\TypeUtils;
use PHPStan\Type\UnionType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\VerbosityLevel;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\GeneralizePrecision;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Reflection\ParametersAcceptorSelector;

class CQC_Phpstan_Service_Rule_ModelProperty_ModelPropertiesRuleHelper {
    /**
     * @param MethodReflection     $methodReflection
     * @param Scope                $scope
     * @param Node\Arg[]           $args
     * @param null|ClassReflection $modelReflection
     *
     * @throws ShouldNotHappenException
     *
     * @return string[]
     */
    public function check(MethodReflection $methodReflection, Scope $scope, array $args, ?ClassReflection $modelReflection = null): array {
        $modelPropertyParameter = $this->hasModelPropertyParameter($methodReflection, $scope, $args, $modelReflection);

        if (count($modelPropertyParameter) !== 2) {
            return [];
        }

        /** @var int $parameterIndex */
        /** @var Type $modelType */
        list($parameterIndex, $modelType) = $modelPropertyParameter;

        if (!(new ObjectType(CModel::class))->isSuperTypeOf($modelType)->yes() || $modelType->equals(new ObjectType(CModel::class))) {
            return [];
        }

        if (!array_key_exists($parameterIndex, $args)) {
            return [];
        }

        $argValue = $args[$parameterIndex]->value;

        if (!$argValue instanceof Node\Expr) {
            return [];
        }

        $argType = $scope->getType($argValue);

        if ($argType instanceof ConstantArrayType) {
            $errors = [];

            $keyType = TypeUtils::generalizeType($argType->getKeyType(), GeneralizePrecision::lessSpecific());

            if ($keyType instanceof IntegerType) {
                $valueTypes = $argType->getValuesArray()->getValueTypes();
            } elseif ($keyType instanceof StringType) {
                $valueTypes = $argType->getKeysArray()->getValueTypes();
            } else {
                $valueTypes = [];
            }

            foreach ($valueTypes as $valueType) {
                // It could be something like `DB::raw`
                // We only want to analyze strings
                if (!$valueType instanceof ConstantStringType) {
                    continue;
                }

                // TODO: maybe check table names and columns here. And for JSON access maybe just the column name
                if (mb_strpos($valueType->getValue(), '.') !== false || mb_strpos($valueType->getValue(), '->') !== false) {
                    continue;
                }

                if (!$modelType->hasProperty($valueType->getValue())->yes()) {
                    $error = sprintf('Property \'%s\' does not exist in %s model.', $valueType->getValue(), $modelType->describe(VerbosityLevel::typeOnly()));

                    if ($methodReflection->getDeclaringClass()->getName() === CModel_Relation_BelongsToMany::class) {
                        $error .= sprintf(" If '%s' exists as a column on the pivot table, consider using 'wherePivot' or prefix the column with table name instead.", $valueType->getValue());
                    }

                    $errors[] = $error;
                }
            }

            return $errors;
        }

        if (!$argType instanceof ConstantStringType) {
            return [];
        }

        // TODO: maybe check table names and columns here. And for JSON access maybe just the column name
        if (mb_strpos($argType->getValue(), '.') !== false || mb_strpos($argType->getValue(), '->') !== false) {
            return [];
        }

        if (!$modelType->hasProperty($argType->getValue())->yes()) {
            $error = sprintf('Property \'%s\' does not exist in %s model.', $argType->getValue(), $modelType->describe(VerbosityLevel::typeOnly()));

            if ((new ObjectType(CModel_Relation_BelongsToMany::class))->isSuperTypeOf(ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType())->yes()) {
                $error .= sprintf(" If '%s' exists as a column on the pivot table, consider using 'wherePivot' or prefix the column with table name instead.", $argType->getValue());
            }

            return [$error];
        }

        return [];
    }

    /**
     * @param MethodReflection     $methodReflection
     * @param Scope                $scope
     * @param Node\Arg[]           $args
     * @param null|ClassReflection $modelReflection
     *
     * @return array<int, int|Type>
     */
    public function hasModelPropertyParameter(
        MethodReflection $methodReflection,
        Scope $scope,
        array $args,
        ?ClassReflection $modelReflection = null
    ): array {
        $parameters = ParametersAcceptorSelector::selectFromArgs($scope, $args, $methodReflection->getVariants())->getParameters();

        foreach ($parameters as $index => $parameter) {
            $type = $parameter->getType();

            if ($type instanceof UnionType) {
                foreach ($type->getTypes() as $innerType) {
                    if ($innerType instanceof CQC_Phpstan_Service_Type_ModelProperty_GenericModelPropertyType) {
                        return [$index, $innerType->getGenericType()];
                    }

                    if ($innerType instanceof CQC_Phpstan_Service_Type_ModelProperty_ModelPropertyType && $modelReflection !== null) {
                        return [$index, new ObjectType($modelReflection->getName())];
                    }
                }
            } elseif ($type instanceof ArrayType) {
                $keyType = $type->getKeyType();
                $itemType = $type->getItemType();

                if ($keyType instanceof CQC_Phpstan_Service_Type_ModelProperty_GenericModelPropertyType) {
                    return [$index, $keyType->getGenericType()];
                }

                if ($itemType instanceof CQC_Phpstan_Service_Type_ModelProperty_GenericModelPropertyType) {
                    return [$index, $itemType->getGenericType()];
                }

                if ($modelReflection !== null && (($keyType instanceof CQC_Phpstan_Service_Type_ModelProperty_ModelPropertyType) || ($itemType instanceof CQC_Phpstan_Service_Type_ModelProperty_ModelPropertyType))) {
                    return [$index, new ObjectType($modelReflection->getName())];
                }
            } else {
                if ($type instanceof CQC_Phpstan_Service_Type_ModelProperty_GenericModelPropertyType) {
                    return [$index, $type->getGenericType()];
                }

                if ($modelReflection !== null && $type instanceof CQC_Phpstan_Service_Type_ModelProperty_ModelPropertyType) {
                    return [$index, new ObjectType($modelReflection->getName())];
                }
            }
        }

        return [];
    }
}
