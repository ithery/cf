<?php

use PhpParser\Node;
use PhpParser\NodeFinder;
use PHPStan\Type\ObjectType;
use PHPStan\Parser\SimpleParser;
use PHPStan\Analyser\ScopeContext;
use PHPStan\Analyser\ScopeFactory;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\Generic\GenericClassStringType;

class CQC_Phpstan_Service_RelationParserHelper {
    /**
     * @var SimpleParser
     */
    private $parser;

    /**
     * @var ScopeFactory
     */
    private $scopeFactory;

    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    public function __construct(SimpleParser $parser, ScopeFactory $scopeFactory, ReflectionProvider $reflectionProvider) {
        $this->parser = $parser;
        $this->scopeFactory = $scopeFactory;
        $this->reflectionProvider = $reflectionProvider;
    }

    public function findRelatedModelInRelationMethod(
        MethodReflection $methodReflection
    ): ?string {
        $fileName = $methodReflection
            ->getDeclaringClass()
            ->getNativeReflection()
            ->getMethod($methodReflection->getName())
            ->getFileName();

        if ($fileName === false) {
            return null;
        }

        $fileStmts = $this->parser->parseFile($fileName);

        /** @var null|Node\Stmt\ClassMethod $relationMethod */
        $relationMethod = $this->findMethod($methodReflection->getName(), $fileStmts);

        if ($relationMethod === null) {
            return null;
        }

        /** @var null|Node\Stmt\Return_ $returnStmt */
        $returnStmt = $this->findReturn($relationMethod);

        if ($returnStmt === null || !$returnStmt->expr instanceof MethodCall) {
            return null;
        }

        $methodCall = $returnStmt->expr;

        while ($methodCall->var instanceof MethodCall) {
            $methodCall = $methodCall->var;
        }

        if (count($methodCall->args) < 1) {
            return null;
        }

        $scope = $this->scopeFactory->create(
            ScopeContext::create($fileName),
            false,
            [],
            $methodReflection
        );

        $methodScope = $scope
            ->enterClass($methodReflection->getDeclaringClass())
            ->enterClassMethod($relationMethod, TemplateTypeMap::createEmpty(), [], null, null, null, false, false, false);

        $argType = $methodScope->getType($methodCall->args[0]->value);
        $returnClass = null;

        if ($argType instanceof ConstantStringType) {
            $returnClass = $argType->getValue();
        }

        if ($argType instanceof GenericClassStringType) {
            $modelType = $argType->getGenericType();

            if (!$modelType instanceof ObjectType) {
                return null;
            }

            $returnClass = $modelType->getClassName();
        }

        if ($returnClass === null) {
            return null;
        }

        return $this->reflectionProvider->hasClass($returnClass) ? $returnClass : null;
    }

    /**
     * @param string $method
     * @param mixed  $statements
     *
     * @return null|Node
     */
    private function findMethod(string $method, $statements): ?Node {
        return (new NodeFinder())->findFirst($statements, static function (Node $node) use ($method) {
            return $node instanceof Node\Stmt\ClassMethod
                && $node->name->toString() === $method;
        });
    }

    private function findReturn(Node\Stmt\ClassMethod $relationMethod): ?Node {
        /** @var Node[] $statements */
        $statements = $relationMethod->stmts;

        return (new NodeFinder())->findFirstInstanceOf($statements, Node\Stmt\Return_::class);
    }
}
