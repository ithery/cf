<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\Framework\MockObject\Api;

use PHPUnit\Framework\MockObject\InvocationHandler;
use PHPUnit\Framework\MockObject\ConfigurableMethod;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker as InvocationMockerBuilder;
use PHPUnit\Framework\MockObject\Exception\ConfigurableMethodsAlreadyInitializedException;

/**
 * @internal This trait is not covered by the backward compatibility promise for PHPUnit
 */
trait Api {
    /**
     * @var ConfigurableMethod[]
     */
    private static $__phpunit_configurableMethods;

    /**
     * @var object
     */
    private $__phpunit_originalObject;

    /**
     * @var bool
     */
    private $__phpunit_returnValueGeneration = true;

    /**
     * @var InvocationHandler
     */
    private $__phpunit_invocationMocker;

    /**
     * @noinspection MagicMethodsValidityInspection
     */
    public static function __phpunit_initConfigurableMethods(ConfigurableMethod ...$configurableMethods) {
        if (isset(static::$__phpunit_configurableMethods)) {
            throw new ConfigurableMethodsAlreadyInitializedException(
                'Configurable methods is already initialized and can not be reinitialized'
            );
        }

        static::$__phpunit_configurableMethods = $configurableMethods;
    }

    /**
     * @noinspection MagicMethodsValidityInspection
     *
     * @param mixed $originalObject
     */
    public function __phpunit_setOriginalObject($originalObject) {
        $this->__phpunit_originalObject = $originalObject;
    }

    /**
     * @noinspection MagicMethodsValidityInspection
     *
     * @param mixed $returnValueGeneration
     */
    public function __phpunit_setReturnValueGeneration($returnValueGeneration) {
        $this->__phpunit_returnValueGeneration = $returnValueGeneration;
    }

    /**
     * @noinspection MagicMethodsValidityInspection
     */
    public function __phpunit_getInvocationHandler() {
        if ($this->__phpunit_invocationMocker === null) {
            $this->__phpunit_invocationMocker = new InvocationHandler(
                static::$__phpunit_configurableMethods,
                $this->__phpunit_returnValueGeneration
            );
        }

        return $this->__phpunit_invocationMocker;
    }

    /**
     * @noinspection MagicMethodsValidityInspection
     */
    public function __phpunit_hasMatchers() {
        return $this->__phpunit_getInvocationHandler()->hasMatchers();
    }

    /**
     * @noinspection MagicMethodsValidityInspection
     *
     * @param mixed $unsetInvocationMocker
     */
    public function __phpunit_verify($unsetInvocationMocker = true) {
        $this->__phpunit_getInvocationHandler()->verify();

        if ($unsetInvocationMocker) {
            $this->__phpunit_invocationMocker = null;
        }
    }

    public function expects(InvocationOrder $matcher) {
        return $this->__phpunit_getInvocationHandler()->expects($matcher);
    }
}
