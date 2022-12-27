<?php

use PhpParser\Node;
use PHPStan\Type\Type;
use PHPStan\Rules\Rule;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ErrorType;
use PHPStan\Rules\RuleLevelHelper;
use PhpParser\Node\Expr\MethodCall;
use NunoMaduro\Larastan\Rules\ModelRuleHelper;

/**
 * @implements \PHPStan\Rules\Rule<\PhpParser\Node\Expr\MethodCall>
 */
class CQC_Phpstan_Service_Rule_ModelProperty_ModelPropertyRule implements Rule {
    /**
     * @var CQC_Phpstan_Service_Rule_ModelProperty_ModelPropertiesRuleHelper
     */
    private $modelPropertiesRuleHelper;

    /**
     * @var RuleLevelHelper
     */
    private $ruleLevelHelper;

    /**
     * @var CQC_Phpstan_Service_Rule_ModelRuleHelper
     */
    private $modelRuleHelper;

    public function __construct(CQC_Phpstan_Service_Rule_ModelProperty_ModelPropertiesRuleHelper $ruleHelper, RuleLevelHelper $ruleLevelHelper, CQC_Phpstan_Service_Rule_ModelRuleHelper $modelRuleHelper) {
        $this->modelPropertiesRuleHelper = $ruleHelper;
        $this->ruleLevelHelper = $ruleLevelHelper;
        $this->modelRuleHelper = $modelRuleHelper;
    }

    public function getNodeType(): string {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @param Scope      $scope
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

        $name = $node->name->name;
        $typeResult = $this->ruleLevelHelper->findTypeToCheck(
            $scope,
            $node->var,
            '',
            static function (Type $type) use ($name): bool {
                return $type->canCallMethods()->yes() && $type->hasMethod($name)->yes();
            }
        );

        $type = $typeResult->getType();

        if ($type instanceof ErrorType) {
            return [];
        }

        if (!$type->hasMethod($name)->yes()) {
            return [];
        }

        $modelReflection = $this->modelRuleHelper->findModelReflectionFromType($type);

        $methodReflection = $type->getMethod($name, $scope);

        return $this->modelPropertiesRuleHelper->check($methodReflection, $scope, $node->getArgs(), $modelReflection);
    }
}
