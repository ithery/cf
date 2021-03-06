<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\Framework\MockObject;

use function assert;
use function implode;
use function sprintf;
use PHPUnit\Framework\TestFailure;
use PHPUnit\Framework\MockObject\Stub\Stub;
use PHPUnit\Framework\MockObject\Rule\MethodName;
use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use PHPUnit\Framework\MockObject\Rule\AnyParameters;
use PHPUnit\Framework\MockObject\Rule\ParametersRule;
use PHPUnit\Framework\MockObject\Rule\AnyInvokedCount;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use PHPUnit\Framework\Exception\ExpectationFailedException;
use PHPUnit\Framework\MockObject\Exception\MatchBuilderNotFoundException;
use PHPUnit\Framework\MockObject\Exception\MethodNameNotConfiguredException;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class Matcher {
    /**
     * @var InvocationOrder
     */
    private $invocationRule;

    /**
     * @var mixed
     */
    private $afterMatchBuilderId;

    /**
     * @var bool
     */
    private $afterMatchBuilderIsInvoked = false;

    /**
     * @var MethodName
     */
    private $methodNameRule;

    /**
     * @var ParametersRule
     */
    private $parametersRule;

    /**
     * @var Stub
     */
    private $stub;

    public function __construct(InvocationOrder $rule) {
        $this->invocationRule = $rule;
    }

    public function hasMatchers() {
        return !$this->invocationRule instanceof AnyInvokedCount;
    }

    public function hasMethodNameRule() {
        return $this->methodNameRule !== null;
    }

    public function getMethodNameRule() {
        return $this->methodNameRule;
    }

    public function setMethodNameRule(MethodName $rule) {
        $this->methodNameRule = $rule;
    }

    public function hasParametersRule() {
        return $this->parametersRule !== null;
    }

    public function setParametersRule(ParametersRule $rule) {
        $this->parametersRule = $rule;
    }

    public function setStub(Stub $stub) {
        $this->stub = $stub;
    }

    public function setAfterMatchBuilderId($id) {
        $this->afterMatchBuilderId = $id;
    }

    /**
     * @throws MethodNameNotConfiguredException
     * @throws MatchBuilderNotFoundException
     * @throws RuntimeException
     * @throws ExpectationFailedException
     */
    public function invoked(Invocation $invocation) {
        if ($this->methodNameRule === null) {
            throw new MethodNameNotConfiguredException();
        }

        if ($this->afterMatchBuilderId !== null) {
            $matcher = $invocation->getObject()
                ->__phpunit_getInvocationHandler()
                ->lookupMatcher($this->afterMatchBuilderId);

            if (!$matcher) {
                throw new MatchBuilderNotFoundException($this->afterMatchBuilderId);
            }

            assert($matcher instanceof self);

            if ($matcher->invocationRule->hasBeenInvoked()) {
                $this->afterMatchBuilderIsInvoked = true;
            }
        }

        $this->invocationRule->invoked($invocation);

        try {
            if ($this->parametersRule !== null) {
                $this->parametersRule->apply($invocation);
            }
        } catch (ExpectationFailedException $e) {
            throw new ExpectationFailedException(
                sprintf(
                    "Expectation failed for %s when %s\n%s",
                    $this->methodNameRule->toString(),
                    $this->invocationRule->toString(),
                    $e->getMessage()
                ),
                $e->getComparisonFailure()
            );
        }

        if ($this->stub) {
            return $this->stub->invoke($invocation);
        }

        return $invocation->generateReturnValue();
    }

    /**
     * @throws RuntimeException
     * @throws MethodNameNotConfiguredException
     * @throws MatchBuilderNotFoundException
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function matches(Invocation $invocation) {
        if ($this->afterMatchBuilderId !== null) {
            $matcher = $invocation->getObject()
                ->__phpunit_getInvocationHandler()
                ->lookupMatcher($this->afterMatchBuilderId);

            if (!$matcher) {
                throw new MatchBuilderNotFoundException($this->afterMatchBuilderId);
            }

            assert($matcher instanceof self);

            if (!$matcher->invocationRule->hasBeenInvoked()) {
                return false;
            }
        }

        if ($this->methodNameRule === null) {
            throw new MethodNameNotConfiguredException();
        }

        if (!$this->invocationRule->matches($invocation)) {
            return false;
        }

        try {
            if (!$this->methodNameRule->matches($invocation)) {
                return false;
            }
        } catch (ExpectationFailedException $e) {
            throw new ExpectationFailedException(
                sprintf(
                    "Expectation failed for %s when %s\n%s",
                    $this->methodNameRule->toString(),
                    $this->invocationRule->toString(),
                    $e->getMessage()
                ),
                $e->getComparisonFailure()
            );
        }

        return true;
    }

    /**
     * @throws MethodNameNotConfiguredException
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function verify() {
        if ($this->methodNameRule === null) {
            throw new MethodNameNotConfiguredException();
        }

        try {
            $this->invocationRule->verify();

            if ($this->parametersRule === null) {
                $this->parametersRule = new AnyParameters();
            }

            $invocationIsAny = $this->invocationRule instanceof AnyInvokedCount;
            $invocationIsNever = $this->invocationRule instanceof InvokedCount && $this->invocationRule->isNever();

            if (!$invocationIsAny && !$invocationIsNever) {
                $this->parametersRule->verify();
            }
        } catch (ExpectationFailedException $e) {
            throw new ExpectationFailedException(
                sprintf(
                    "Expectation failed for %s when %s.\n%s",
                    $this->methodNameRule->toString(),
                    $this->invocationRule->toString(),
                    TestFailure::exceptionToString($e)
                )
            );
        }
    }

    public function toString() {
        $list = [];

        if ($this->invocationRule !== null) {
            $list[] = $this->invocationRule->toString();
        }

        if ($this->methodNameRule !== null) {
            $list[] = 'where ' . $this->methodNameRule->toString();
        }

        if ($this->parametersRule !== null) {
            $list[] = 'and ' . $this->parametersRule->toString();
        }

        if ($this->afterMatchBuilderId !== null) {
            $list[] = 'after ' . $this->afterMatchBuilderId;
        }

        if ($this->stub !== null) {
            $list[] = 'will ' . $this->stub->toString();
        }

        return implode(' ', $list);
    }
}
