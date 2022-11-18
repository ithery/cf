<?php

use PhpParser\Node;
use PHPStan\Rules\Rule;
use PHPStan\Analyser\Scope;
use PhpParser\Node\Scalar\String_;
use PHPStan\Reflection\ReflectionProvider;

/**
 * @implements Rule<String_>
 */
final class CQC_Phpstan_Service_Rule_StringToClassRule implements Rule {
    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    public function __construct(ReflectionProvider $reflectionProvider) {
        $this->reflectionProvider = $reflectionProvider;
    }

    public function getNodeType(): string {
        return String_::class;
    }

    /**
     * @return string[] errors
     */
    public function processNode(Node $node, Scope $scope): array {
        $className = $node->value;
        if (isset($className[0]) && '\\' === $className[0]) {
            $className = \substr($className, 1);
        }
        $messages = [];
        if (!$this->startsWithUpper($className)) {
            return $messages;
        }
        if (!\preg_match('/^\\w.+\\w$/u', $className)) {
            return $messages;
        }
        if (!$this->reflectionProvider->hasClass($className)) {
            return $messages;
        }

        $classRef = $this->reflectionProvider->getClass($className)->getNativeReflection();
        if ($classRef->isInternal() && $classRef->getName() !== $className) {
            return $messages;
        }

        return [
            \sprintf('Class %s should be written with ::class notation, string found.', $className),
        ];
    }

    private function startsWithUpper($str) {
        $chr = mb_substr($str, 0, 1, 'UTF-8');

        return mb_strtolower($chr, 'UTF-8') != $chr;
    }
}
