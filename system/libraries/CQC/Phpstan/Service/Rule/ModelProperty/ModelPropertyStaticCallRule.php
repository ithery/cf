<?php

use PhpParser\Node;
use PHPStan\Type\Type;
use PHPStan\Rules\Rule;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ErrorType;
use PHPStan\Type\ObjectType;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\Constant\ConstantStringType;

/**
 * @implements \PHPStan\Rules\Rule<\PhpParser\Node\Expr\StaticCall>
 */
class CQC_Phpstan_Service_Rule_ModelProperty_ModelPropertyStaticCallRule implements Rule {
    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    /**
     * @var CQC_Phpstan_Service_Rule_ModelProperty_ModelPropertiesRuleHelper
     */
    private $modelPropertiesRuleHelper;

    /**
     * @var RuleLevelHelper
     */
    private $ruleLevelHelper;

    public function __construct(ReflectionProvider $reflectionProvider, CQC_Phpstan_Service_Rule_ModelProperty_ModelPropertiesRuleHelper $ruleHelper, RuleLevelHelper $ruleLevelHelper) {
        $this->reflectionProvider = $reflectionProvider;
        $this->modelPropertiesRuleHelper = $ruleHelper;
        $this->ruleLevelHelper = $ruleLevelHelper;
    }

    public function getNodeType(): string {
        return Node\Expr\StaticCall::class;
    }

    /**
     * @param Node\Expr\StaticCall $node
     * @param Scope                $scope
     *
     * @throws \PHPStan\ShouldNotHappenException
     *
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array {
        if (!$node->name instanceof Node\Identifier) {
            return [];
        }

        if (count($node->getArgs()) === 0) {
            return [];
        }

        $methodName = $node->name->name;

        $class = $node->class;

        if ($class instanceof Node\Name) {
            $className = (string) $class;
            $lowercasedClassName = strtolower($className);

            if (in_array($lowercasedClassName, ['self', 'static'], true)) {
                if (!$scope->isInClass()) {
                    return [];
                }

                $modelReflection = $scope->getClassReflection();
            } elseif ($lowercasedClassName === 'parent') {
                if (!$scope->isInClass()) {
                    return [];
                }

                $currentClassReflection = $scope->getClassReflection();

                if ($currentClassReflection === null) {
                    return [];
                }

                $parentClass = $currentClassReflection->getParentClass();

                if ($parentClass === null) {
                    return [];
                }

                if ($scope->getFunctionName() === null) {
                    throw new \PHPStan\ShouldNotHappenException();
                }

                $modelReflection = $parentClass;
            } else {
                if (!$this->reflectionProvider->hasClass($className)) {
                    return [];
                }

                $modelReflection = $this->reflectionProvider->getClass($className);
            }
        } else {
            $classTypeResult = $this->ruleLevelHelper->findTypeToCheck(
                $scope,
                $class,
                '',
                static function (Type $type) use ($methodName): bool {
                    return $type->canCallMethods()->yes() && $type->hasMethod($methodName)->yes();
                }
            );

            $classType = $classTypeResult->getType();

            if ($classType instanceof ErrorType) {
                return [];
            }

            if ($classType instanceof ConstantStringType) {
                $modelClassName = $classType->getValue();
            } elseif ($classType instanceof ObjectType) {
                $modelClassName = $classType->getClassName();
            } else {
                return [];
            }

            $modelReflection = $this->reflectionProvider->getClass($modelClassName);
        }

        if ($modelReflection === null) {
            return [];
        }

        if (!$modelReflection->isSubclassOf(CModel::class)) {
            return [];
        }

        if (!$modelReflection->hasMethod($methodName)) {
            return [];
        }

        $methodReflection = $modelReflection->getMethod($methodName, $scope);

        $className = $methodReflection->getDeclaringClass()->getName();

        if ($className !== CDatabase_Query_Builder::class
            && $className !== CModel_Query::class
            && $className !== CModel_Relation::class
            && $className !== CModel::class
        ) {
            return [];
        }

        return $this->modelPropertiesRuleHelper->check($methodReflection, $scope, $node->getArgs(), $modelReflection);
    }
}
