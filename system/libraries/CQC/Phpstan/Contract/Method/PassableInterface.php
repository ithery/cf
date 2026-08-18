<?php
/**
 * This file is part of PhpStorm.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Reflection\Php\PhpMethodReflectionFactory;

/**
 * @internal
 */
interface CQC_Phpstan_Contract_Method_PassableInterface {
    /**
     * @param \CContainer_ContainerInterface $container
     *
     * @return void
     */
    public function setContainer(CContainer_ContainerInterface $container): void;

    /**
     * @return \PHPStan\Reflection\ClassReflection
     */
    public function getClassReflection(): ClassReflection;

    /**
     * @param \PHPStan\Reflection\ClassReflection $classReflection
     *
     * @return CQC_Phpstan_Contract_Method_PassableInterface
     */
    public function setClassReflection(ClassReflection $classReflection): CQC_Phpstan_Contract_Method_PassableInterface;

    /**
     * @return string
     */
    public function getMethodName(): string;

    /**
     * @return bool
     */
    public function hasFound(): bool;

    /**
     * @param string $class
     *
     * @return bool
     */
    public function searchOn(string $class): bool;

    /**
     * @throws \LogicException
     *
     * @return \PHPStan\Reflection\MethodReflection
     */
    public function getMethodReflection(): MethodReflection;

    /**
     * @param \PHPStan\Reflection\MethodReflection $methodReflection
     */
    public function setMethodReflection(MethodReflection $methodReflection): void;

    /**
     * Declares that the provided method can be called statically.
     *
     * @param bool $staticAllowed
     *
     * @return void
     */
    public function setStaticAllowed(bool $staticAllowed): void;

    /**
     * Returns whether the method can be called statically.
     *
     * @return bool
     */
    public function isStaticAllowed(): bool;

    /**
     * @param string $class
     * @param bool   $staticAllowed
     *
     * @return bool
     */
    public function sendToPipeline(string $class, $staticAllowed = false): bool;

    /**
     * @return ReflectionProvider
     */
    public function getReflectionProvider(): ReflectionProvider;

    /**
     * @return \PHPStan\Reflection\Php\PhpMethodReflectionFactory
     */
    public function getMethodReflectionFactory(): PhpMethodReflectionFactory;
}
