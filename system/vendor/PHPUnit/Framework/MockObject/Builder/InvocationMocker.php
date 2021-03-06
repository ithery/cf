<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\Framework\MockObject\Builder;

use Throwable;
use function count;
use function in_array;
use function array_map;
use function is_string;
use function strtolower;
use function array_merge;
use PHPUnit\Framework\MockObject\Rule;
use PHPUnit\Framework\MockObject\Matcher;
use PHPUnit\Framework\MockObject\Stub\Stub;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\MockObject\Stub\Exception;
use PHPUnit\Framework\MockObject\Stub\ReturnSelf;
use PHPUnit\Framework\MockObject\Stub\ReturnStub;
use PHPUnit\Framework\MockObject\InvocationHandler;
use PHPUnit\Framework\MockObject\ConfigurableMethod;
use PHPUnit\Framework\MockObject\Stub\ReturnArgument;
use PHPUnit\Framework\MockObject\Stub\ReturnCallback;
use PHPUnit\Framework\MockObject\Stub\ReturnValueMap;
use PHPUnit\Framework\MockObject\Stub\ReturnReference;
use PHPUnit\Framework\MockObject\Stub\ConsecutiveCalls;
use PHPUnit\Framework\MockObject\Exception\IncompatibleReturnValueException;
use PHPUnit\Framework\MockObject\Exception\MethodNameNotConfiguredException;
use PHPUnit\Framework\MockObject\Exception\MatcherAlreadyRegisteredException;
use PHPUnit\Framework\MockObject\Exception\MethodCannotBeConfiguredException;
use PHPUnit\Framework\MockObject\Exception\MethodNameAlreadyConfiguredException;
use PHPUnit\Framework\MockObject\Exception\MethodParametersAlreadyConfiguredException;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class InvocationMocker implements InvocationStubber, MethodNameMatch {
    /**
     * @var InvocationHandler
     */
    private $invocationHandler;

    /**
     * @var Matcher
     */
    private $matcher;

    /**
     * @var ConfigurableMethod[]
     */
    private $configurableMethods;

    public function __construct(InvocationHandler $handler, Matcher $matcher, ConfigurableMethod ...$configurableMethods) {
        $this->invocationHandler = $handler;
        $this->matcher = $matcher;
        $this->configurableMethods = $configurableMethods;
    }

    /**
     * @param mixed $id
     *
     * @throws MatcherAlreadyRegisteredException
     *
     * @return $this
     */
    public function id($id) {
        $this->invocationHandler->registerMatcher($id, $this->matcher);

        return $this;
    }

    /**
     * @return $this
     */
    public function will(Stub $stub) {
        $this->matcher->setStub($stub);

        return $this;
    }

    /**
     * @param mixed   $value
     * @param mixed[] $nextValues
     *
     * @throws IncompatibleReturnValueException
     */
    public function willReturn($value, ...$nextValues) {
        if (count($nextValues) === 0) {
            $this->ensureTypeOfReturnValues([$value]);

            $stub = $value instanceof Stub ? $value : new ReturnStub($value);
        } else {
            $values = array_merge([$value], $nextValues);

            $this->ensureTypeOfReturnValues($values);

            $stub = new ConsecutiveCalls($values);
        }

        return $this->will($stub);
    }

    public function willReturnReference(&$reference) {
        $stub = new ReturnReference($reference);

        return $this->will($stub);
    }

    public function willReturnMap(array $valueMap) {
        $stub = new ReturnValueMap($valueMap);

        return $this->will($stub);
    }

    public function willReturnArgument($argumentIndex) {
        $stub = new ReturnArgument($argumentIndex);

        return $this->will($stub);
    }

    public function willReturnCallback($callback) {
        $stub = new ReturnCallback($callback);

        return $this->will($stub);
    }

    public function willReturnSelf() {
        $stub = new ReturnSelf();

        return $this->will($stub);
    }

    public function willReturnOnConsecutiveCalls(...$values) {
        $stub = new ConsecutiveCalls($values);

        return $this->will($stub);
    }

    public function willThrowException(Throwable $exception) {
        $stub = new Exception($exception);

        return $this->will($stub);
    }

    /**
     * @param mixed $id
     *
     * @return $this
     */
    public function after($id) {
        $this->matcher->setAfterMatchBuilderId($id);

        return $this;
    }

    /**
     * @param mixed[] $arguments
     *
     * @throws MethodNameNotConfiguredException
     * @throws MethodParametersAlreadyConfiguredException
     * @throws \PHPUnit\Framework\Exception
     *
     * @return $this
     */
    public function with(...$arguments) {
        $this->ensureParametersCanBeConfigured();

        $this->matcher->setParametersRule(new Rule\Parameters($arguments));

        return $this;
    }

    /**
     * @param array ...$arguments
     *
     * @throws MethodNameNotConfiguredException
     * @throws MethodParametersAlreadyConfiguredException
     * @throws \PHPUnit\Framework\Exception
     *
     * @return $this
     */
    public function withConsecutive(...$arguments) {
        $this->ensureParametersCanBeConfigured();

        $this->matcher->setParametersRule(new Rule\ConsecutiveParameters($arguments));

        return $this;
    }

    /**
     * @throws MethodNameNotConfiguredException
     * @throws MethodParametersAlreadyConfiguredException
     *
     * @return $this
     */
    public function withAnyParameters() {
        $this->ensureParametersCanBeConfigured();

        $this->matcher->setParametersRule(new Rule\AnyParameters());

        return $this;
    }

    /**
     * @param Constraint|string $constraint
     *
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     * @throws \PHPUnit\Framework\InvalidArgumentException
     *
     * @return $this
     */
    public function method($constraint) {
        if ($this->matcher->hasMethodNameRule()) {
            throw new MethodNameAlreadyConfiguredException();
        }

        $configurableMethodNames = array_map(
            static function (ConfigurableMethod $configurable) {
                return strtolower($configurable->getName());
            },
            $this->configurableMethods
        );

        if (is_string($constraint) && !in_array(strtolower($constraint), $configurableMethodNames, true)) {
            throw new MethodCannotBeConfiguredException($constraint);
        }

        $this->matcher->setMethodNameRule(new Rule\MethodName($constraint));

        return $this;
    }

    /**
     * @throws MethodNameNotConfiguredException
     * @throws MethodParametersAlreadyConfiguredException
     */
    private function ensureParametersCanBeConfigured() {
        if (!$this->matcher->hasMethodNameRule()) {
            throw new MethodNameNotConfiguredException();
        }

        if ($this->matcher->hasParametersRule()) {
            throw new MethodParametersAlreadyConfiguredException();
        }
    }

    private function getConfiguredMethod() {
        $configuredMethod = null;

        foreach ($this->configurableMethods as $configurableMethod) {
            if ($this->matcher->getMethodNameRule()->matchesName($configurableMethod->getName())) {
                if ($configuredMethod !== null) {
                    return null;
                }

                $configuredMethod = $configurableMethod;
            }
        }

        return $configuredMethod;
    }

    /**
     * @throws IncompatibleReturnValueException
     */
    private function ensureTypeOfReturnValues(array $values) {
        $configuredMethod = $this->getConfiguredMethod();

        if ($configuredMethod === null) {
            return;
        }

        foreach ($values as $value) {
            if (!$configuredMethod->mayReturn($value)) {
                throw new IncompatibleReturnValueException(
                    $configuredMethod,
                    $value
                );
            }
        }
    }
}
