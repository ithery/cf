<?php

use PhpParser\Node;
use PHPStan\Rules\Rule;
use PhpParser\Node\Expr;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use PHPStan\Type\ObjectType;
use PhpParser\Node\Identifier;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Rules\RuleErrorBuilder;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Reflection\ReflectionProvider;

/**
 * Catches inefficient instantiation of models using Model::make().
 *
 * For example:
 * User::make()
 *
 * It is functionally equivalent to simply use the constructor:
 * new User()
 *
 * @implements Rule<StaticCall>
 */
class CQC_Phpstan_Service_Rule_NoModelMakeRule implements Rule {
    protected ReflectionProvider $reflectionProvider;

    public function __construct($reflectionProvider) {
        $this->reflectionProvider = $reflectionProvider;
    }

    public function getNodeType(): string {
        return StaticCall::class;
    }

    /**
     * @return array<int, RuleError>
     */
    public function processNode(Node $node, Scope $scope): array {
        $name = $node->name;

        if (!$name instanceof Identifier) {
            return [];
        }

        if ($name->name !== 'make') {
            return [];
        }

        if (!$this->isCalledOnModel($node, $scope)) {
            return [];
        }

        return [
            RuleErrorBuilder::message("Called 'Model::make()' which performs unnecessary work, use 'new Model()'.")
                ->identifier('larastan.noModelMake')
                ->line($node->getStartLine())
                ->file($scope->getFile(), $scope->getFileDescription())
                ->build(),
        ];
    }

    /**
     * Was the expression called on a Model instance?
     */
    protected function isCalledOnModel(StaticCall $call, Scope $scope): bool {
        $class = $call->class;
        if ($class instanceof FullyQualified) {
            $type = new ObjectType($class->toString());
        } elseif ($class instanceof Expr) {
            $type = $scope->getType($class);

            if ($type->isClassString()->yes() && $type->getConstantStrings() !== []) {
                $type = new ObjectType($type->getConstantStrings()[0]->getValue());
            }
        } else {
            // TODO can we handle relative names, do they even occur here?
            return false;
        }

        return (new ObjectType(Model::class))
            ->isSuperTypeOf($type)
            ->yes();
    }
}
