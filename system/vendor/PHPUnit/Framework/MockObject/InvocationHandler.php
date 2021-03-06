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

use Exception;
use Throwable;
use function strtolower;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\MockObject\Exception\MatcherAlreadyRegisteredException;
use PHPUnit\Framework\MockObject\Exception\ReturnValueNotConfiguredException;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class InvocationHandler {
    /**
     * @var Matcher[]
     */
    private $matchers = [];

    /**
     * @var Matcher[]
     */
    private $matcherMap = [];

    /**
     * @var ConfigurableMethod[]
     */
    private $configurableMethods;

    /**
     * @var bool
     */
    private $returnValueGeneration;

    /**
     * @var Throwable
     */
    private $deferredError;

    public function __construct(array $configurableMethods, $returnValueGeneration) {
        $this->configurableMethods = $configurableMethods;
        $this->returnValueGeneration = $returnValueGeneration;
    }

    public function hasMatchers() {
        foreach ($this->matchers as $matcher) {
            if ($matcher->hasMatchers()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Looks up the match builder with identification $id and returns it.
     *
     * @param string $id The identification of the match builder
     */
    public function lookupMatcher($id) {
        if (isset($this->matcherMap[$id])) {
            return $this->matcherMap[$id];
        }

        return null;
    }

    /**
     * Registers a matcher with the identification $id. The matcher can later be
     * looked up using lookupMatcher() to figure out if it has been invoked.
     *
     * @param string  $id      The identification of the matcher
     * @param Matcher $matcher The builder which is being registered
     *
     * @throws MatcherAlreadyRegisteredException
     */
    public function registerMatcher($id, Matcher $matcher) {
        if (isset($this->matcherMap[$id])) {
            throw new MatcherAlreadyRegisteredException($id);
        }

        $this->matcherMap[$id] = $matcher;
    }

    public function expects(InvocationOrder $rule) {
        $matcher = new Matcher($rule);
        $this->addMatcher($matcher);

        return new InvocationMocker(
            $this,
            $matcher,
            ...$this->configurableMethods
        );
    }

    /**
     * @throws RuntimeException
     * @throws Exception
     */
    public function invoke(Invocation $invocation) {
        $exception = null;
        $hasReturnValue = false;
        $returnValue = null;

        foreach ($this->matchers as $match) {
            try {
                if ($match->matches($invocation)) {
                    $value = $match->invoked($invocation);

                    if (!$hasReturnValue) {
                        $returnValue = $value;
                        $hasReturnValue = true;
                    }
                }
            } catch (Exception $e) {
                $exception = $e;
            }
        }

        if ($exception !== null) {
            throw $exception;
        }

        if ($hasReturnValue) {
            return $returnValue;
        }

        if (!$this->returnValueGeneration) {
            $exception = new ReturnValueNotConfiguredException($invocation);

            if (strtolower($invocation->getMethodName()) === '__tostring') {
                $this->deferredError = $exception;

                return '';
            }

            throw $exception;
        }

        return $invocation->generateReturnValue();
    }

    public function matches(Invocation $invocation) {
        foreach ($this->matchers as $matcher) {
            if (!$matcher->matches($invocation)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws Throwable
     */
    public function verify() {
        foreach ($this->matchers as $matcher) {
            $matcher->verify();
        }

        if ($this->deferredError) {
            throw $this->deferredError;
        }
    }

    private function addMatcher(Matcher $matcher) {
        $this->matchers[] = $matcher;
    }
}
