<?php

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Reflection\Php\PhpMethodReflection;
use PHPStan\Reflection\Php\PhpMethodReflectionFactory;

/**
 * @internal
 */
final class CQC_Phpstan_Method_Passable implements CQC_Phpstan_Contract_Method_PassableInterface {
    use CQC_Phpstan_Concern_HasContainer;

    /**
     * @var \PHPStan\Reflection\Php\PhpMethodReflectionFactory
     */
    private $methodReflectionFactory;

    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    /**
     * @var \CBase_PipelineInterface
     */
    private $pipeline;

    /**
     * @var \PHPStan\Reflection\ClassReflection
     */
    private $classReflection;

    /**
     * @var string
     */
    private $methodName;

    /**
     * @var null|\PHPStan\Reflection\MethodReflection
     */
    private $methodReflection;

    /**
     * @var bool
     */
    private $staticAllowed = false;

    /**
     * Method constructor.
     *
     * @param \PHPStan\Reflection\Php\PhpMethodReflectionFactory $methodReflectionFactory
     * @param ReflectionProvider                                 $reflectionProvider
     * @param \CBase_PipelineInterface                           $pipeline
     * @param \PHPStan\Reflection\ClassReflection                $classReflection
     * @param string                                             $methodName
     */
    public function __construct(
        PhpMethodReflectionFactory $methodReflectionFactory,
        ReflectionProvider $reflectionProvider,
        CBase_PipelineInterface $pipeline,
        ClassReflection $classReflection,
        string $methodName
    ) {
        $this->methodReflectionFactory = $methodReflectionFactory;
        $this->reflectionProvider = $reflectionProvider;
        $this->pipeline = $pipeline;
        $this->classReflection = $classReflection;
        $this->methodName = $methodName;
    }

    /**
     * @inheritdoc
     */
    public function getClassReflection(): ClassReflection {
        return $this->classReflection;
    }

    /**
     * @inheritdoc
     */
    public function setClassReflection(ClassReflection $classReflection): CQC_Phpstan_Contract_Method_PassableInterface {
        $this->classReflection = $classReflection;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMethodName(): string {
        return $this->methodName;
    }

    /**
     * @inheritdoc
     */
    public function hasFound(): bool {
        return $this->methodReflection !== null;
    }

    /**
     * @inheritdoc
     */
    public function searchOn(string $class): bool {
        $classReflection = $this->reflectionProvider->getClass($class);

        $found = $classReflection->hasNativeMethod($this->methodName);

        if ($found) {
            $this->setMethodReflection($classReflection->getNativeMethod($this->methodName));
        }

        return $found;
    }

    /**
     * @inheritdoc
     */
    public function getMethodReflection(): MethodReflection {
        if ($this->methodReflection === null) {
            throw new LogicException("MethodReflection doesn't exist");
        }

        return $this->methodReflection;
    }

    /**
     * @inheritdoc
     */
    public function setMethodReflection(MethodReflection $methodReflection): void {
        $this->methodReflection = $methodReflection;
    }

    /**
     * @inheritdoc
     */
    public function setStaticAllowed(bool $staticAllowed): void {
        $this->staticAllowed = $staticAllowed;
    }

    /**
     * @inheritdoc
     */
    public function isStaticAllowed(): bool {
        return $this->staticAllowed;
    }

    /**
     * @inheritdoc
     */
    public function sendToPipeline(string $class, $staticAllowed = false): bool {
        $classReflection = $this->reflectionProvider->getClass($class);

        $this->setStaticAllowed($this->staticAllowed ?: $staticAllowed);

        $originalClassReflection = $this->classReflection;
        $this->pipeline->send($this->setClassReflection($classReflection))
            ->then(
                function (CQC_Phpstan_Contract_Method_PassableInterface $passable) use ($originalClassReflection) {
                    if ($passable->hasFound()) {
                        $this->setMethodReflection($passable->getMethodReflection());
                        $this->setStaticAllowed($passable->isStaticAllowed());
                    }

                    $this->setClassReflection($originalClassReflection);
                }
            );

        if ($result = $this->hasFound()) {
            $methodReflection = $this->getMethodReflection();
            if (get_class($methodReflection) === PhpMethodReflection::class) {
                $methodReflection = Mockery::mock($methodReflection);
                $methodReflection->shouldReceive('isStatic')
                    ->andReturn($this->isStaticAllowed());
            }

            $this->setMethodReflection($methodReflection);
        }

        return $result;
    }

    public function getReflectionProvider(): ReflectionProvider {
        return $this->reflectionProvider;
    }

    /**
     * @inheritdoc
     */
    public function getMethodReflectionFactory(): PhpMethodReflectionFactory {
        return $this->methodReflectionFactory;
    }
}
