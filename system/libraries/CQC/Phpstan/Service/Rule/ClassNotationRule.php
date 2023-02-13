<?php

use PhpParser\Node;
use PHPStan\Rules\Rule;
use PHPStan\Analyser\Scope;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Interface_;
use PHPStan\Reflection\ReflectionProvider;

/**
 * @implements Rule<ClassLike>
 */
final class CQC_Phpstan_Service_Rule_ClassNotationRule implements Rule {
    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    public function __construct(ReflectionProvider $reflectionProvider) {
        $this->reflectionProvider = $reflectionProvider;
    }

    public function getNodeType(): string {
        return ClassLike::class;
    }

    /**
     * @return string[] errors
     */
    public function processNode(Node $node, Scope $scope): array {
        $messages = [];
        $nodeIdentifier = $node->name;
        if (null === $nodeIdentifier) {
            return $messages;
        }
        $name = $nodeIdentifier->name;
        if (0 === \strpos($name, 'AnonymousClass')) {
            return $messages;
        }

        $fqcn = $node->namespacedName->toString();
        if ($node instanceof Interface_) {
            if (!\preg_match('/Interface$/', $name)) {
                // $messages[] = \sprintf('Interface %s should end with "Interface" suffix.', $fqcn);
            }
        } elseif ($node instanceof Trait_) {
            if (!\preg_match('/Trait$/', $name)) {
                $messages[] = \sprintf('Trait %s should end with "Trait" suffix.', $fqcn);
            }
        } else {
            $classRef = $this->reflectionProvider->getClass($fqcn)->getNativeReflection();

            if ($classRef->isSubclassOf(Exception::class)) {
                if (!\preg_match('/Exception$/', $name)) {
                    $messages[] = \sprintf('Exception class %s should end with "Exception" suffix.', $fqcn);
                }
            }
        }

        return $messages;
    }
}
