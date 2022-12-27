<?php

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Reflection\Php\PhpMethodReflectionFactory;
use NunoMaduro\Larastan\Contracts\Methods\PassableContract;

/**
 * @internal
 */
final class CQC_Phpstan_Service_Method_Kernel {
    use CQC_Phpstan_Concern_HasContainer;

    /**
     * @var PhpMethodReflectionFactory
     */
    private $methodReflectionFactory;

    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    /**
     * Kernel constructor.
     *
     * @param PhpMethodReflectionFactory $methodReflectionFactory
     */
    public function __construct(
        PhpMethodReflectionFactory $methodReflectionFactory,
        ReflectionProvider $reflectionProvider
    ) {
        $this->methodReflectionFactory = $methodReflectionFactory;
        $this->reflectionProvider = $reflectionProvider;
    }

    /**
     * @param ClassReflection $classReflection
     * @param string          $methodName
     *
     * @return CQC_Phpstan_Contract_Method_PassableInterface
     */
    public function handle(ClassReflection $classReflection, string $methodName): CQC_Phpstan_Contract_Method_PassableInterface {
        $pipeline = new CBase_Pipeline($this->getContainer());

        $passable = new CQC_Phpstan_Service_Method_Passable($this->methodReflectionFactory, $this->reflectionProvider, $pipeline, $classReflection, $methodName);

        $pipeline->send($passable)
            ->through(
                [
                    CQC_Phpstan_Service_Method_Pipe_SelfClass::class,
                ]
            )
            ->then(
                function ($method) {
                }
            );

        return $passable;
    }
}
