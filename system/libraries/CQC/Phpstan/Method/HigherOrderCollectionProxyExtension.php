<?php

use PHPStan\Type;
use PHPStan\TrinaryLogic;
use PHPStan\Analyser\OutOfClassScope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\MethodsClassReflectionExtension;

final class CQC_Phpstan_Method_HigherOrderCollectionProxyExtension implements MethodsClassReflectionExtension {
    public function hasMethod(ClassReflection $classReflection, string $methodName): bool {
        $result = CQC_Phpstan_Service_Support_HigherOrderCollectionProxyHelper::hasPropertyOrMethod($classReflection, $methodName, 'method');

        return $result;
    }

    public function getMethod(
        ClassReflection $classReflection,
        string $methodName
    ): MethodReflection {
        $activeTemplateTypeMap = $classReflection->getActiveTemplateTypeMap();

        /** @var Type\Constant\ConstantStringType $methodType */
        $methodType = $activeTemplateTypeMap->getType('T');

        /** @var Type\ObjectType $valueType */
        $valueType = $activeTemplateTypeMap->getType('TValue');

        $modelMethodReflection = $valueType->getMethod($methodName, new OutOfClassScope());

        $modelMethodReturnType = ParametersAcceptorSelector::selectSingle($modelMethodReflection->getVariants())->getReturnType();
        $returnType = CQC_Phpstan_Service_Support_HigherOrderCollectionProxyHelper::determineReturnType($methodType->getValue(), $valueType, $modelMethodReturnType);

        return new class($classReflection, $methodName, $modelMethodReflection, $returnType) implements MethodReflection {
            /**
             * @var ClassReflection
             */
            private $classReflection;

            /**
             * @var string
             */
            private $methodName;

            /**
             * @var MethodReflection
             */
            private $modelMethodReflection;

            /**
             * @var Type\Type
             */
            private $returnType;

            public function __construct(ClassReflection $classReflection, string $methodName, MethodReflection $modelMethodReflection, Type\Type $returnType) {
                $this->classReflection = $classReflection;
                $this->methodName = $methodName;
                $this->modelMethodReflection = $modelMethodReflection;
                $this->returnType = $returnType;
            }

            public function getDeclaringClass(): ClassReflection {
                return $this->classReflection;
            }

            public function isStatic(): bool {
                return false;
            }

            public function isPrivate(): bool {
                return false;
            }

            public function isPublic(): bool {
                return true;
            }

            public function getDocComment(): ?string {
                return null;
            }

            public function getName(): string {
                return $this->methodName;
            }

            public function getPrototype(): PHPStan\Reflection\ClassMemberReflection {
                return $this;
            }

            public function getVariants(): array {
                return [
                    new FunctionVariant(
                        ParametersAcceptorSelector::selectSingle($this->modelMethodReflection->getVariants())->getTemplateTypeMap(),
                        ParametersAcceptorSelector::selectSingle($this->modelMethodReflection->getVariants())->getResolvedTemplateTypeMap(),
                        ParametersAcceptorSelector::selectSingle($this->modelMethodReflection->getVariants())->getParameters(),
                        ParametersAcceptorSelector::selectSingle($this->modelMethodReflection->getVariants())->isVariadic(),
                        $this->returnType
                    ),
                ];
            }

            public function isDeprecated(): TrinaryLogic {
                return TrinaryLogic::createNo();
            }

            public function getDeprecatedDescription(): ?string {
                return null;
            }

            public function isFinal(): TrinaryLogic {
                return TrinaryLogic::createNo();
            }

            public function isInternal(): TrinaryLogic {
                return TrinaryLogic::createNo();
            }

            public function getThrowType(): ?PHPStan\Type\Type {
                return null;
            }

            public function hasSideEffects(): TrinaryLogic {
                return TrinaryLogic::createMaybe();
            }
        };
    }
}
