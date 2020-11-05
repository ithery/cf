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

use function array_diff;
use function array_merge;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @psalm-template MockedType
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class MockBuilder
{
    /**
     * @var TestCase
     */
    private $testCase;

    /**
     * @var string
     */
    private $type;

    /**
     * @var null|string[]
     */
    private $methods = [];

    /**
     * @var bool
     */
    private $emptyMethodsArray = false;

    /**
     * @var string
     */
    private $mockClassName = '';

    /**
     * @var array
     */
    private $constructorArgs = [];

    /**
     * @var bool
     */
    private $originalConstructor = true;

    /**
     * @var bool
     */
    private $originalClone = true;

    /**
     * @var bool
     */
    private $autoload = true;

    /**
     * @var bool
     */
    private $cloneArguments = false;

    /**
     * @var bool
     */
    private $callOriginalMethods = false;

    /**
     * @var ?object
     */
    private $proxyTarget;

    /**
     * @var bool
     */
    private $allowMockingUnknownTypes = true;

    /**
     * @var bool
     */
    private $returnValueGeneration = true;

    /**
     * @var Generator
     */
    private $generator;

    /**
     * @param string|string[] $type
     *
     * @psalm-param class-string<MockedType>|string|string[] $type
     */
    public function __construct(TestCase $testCase, $type)
    {
        $this->testCase  = $testCase;
        $this->type      = $type;
        $this->generator = new Generator;
    }

    /**
     * Creates a mock object using a fluent interface.
     *
     * @throws UnknownTypeException
     * @throws InvalidMethodNameException
     * @throws DuplicateMethodException
     * @throws ClassIsFinalException
     * @throws ClassAlreadyExistsException
     * @throws OriginalConstructorInvocationRequiredException
     * @throws RuntimeException
     * @throws ReflectionException
     * @throws \PHPUnit\Framework\InvalidArgumentException
     *
     * @psalm-return MockObject&MockedType
     */
    public function getMock()
    {
        $object = $this->generator->getMock(
            $this->type,
            !$this->emptyMethodsArray ? $this->methods : null,
            $this->constructorArgs,
            $this->mockClassName,
            $this->originalConstructor,
            $this->originalClone,
            $this->autoload,
            $this->cloneArguments,
            $this->callOriginalMethods,
            $this->proxyTarget,
            $this->allowMockingUnknownTypes,
            $this->returnValueGeneration
        );

        $this->testCase->registerMockObject($object);

        return $object;
    }

    /**
     * Creates a mock object for an abstract class using a fluent interface.
     *
     * @psalm-return MockObject&MockedType
     *
     * @throws \PHPUnit\Framework\Exception
     * @throws RuntimeException
     * @throws ReflectionException
     */
    public function getMockForAbstractClass()
    {
        $object = $this->generator->getMockForAbstractClass(
            $this->type,
            $this->constructorArgs,
            $this->mockClassName,
            $this->originalConstructor,
            $this->originalClone,
            $this->autoload,
            $this->methods,
            $this->cloneArguments
        );

        $this->testCase->registerMockObject($object);

        return $object;
    }

    /**
     * Creates a mock object for a trait using a fluent interface.
     *
     * @psalm-return MockObject&MockedType
     *
     * @throws \PHPUnit\Framework\Exception
     * @throws ReflectionException
     * @throws RuntimeException
     */
    public function getMockForTrait()
    {
        $object = $this->generator->getMockForTrait(
            $this->type,
            $this->constructorArgs,
            $this->mockClassName,
            $this->originalConstructor,
            $this->originalClone,
            $this->autoload,
            $this->methods,
            $this->cloneArguments
        );

        $this->testCase->registerMockObject($object);

        return $object;
    }

    /**
     * Specifies the subset of methods to mock. Default is to mock none of them.
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/pull/3687
     *
     * @return $this
     */
    public function setMethods($methods = null)
    {
        if ($methods === null) {
            $this->methods = $methods;
        } else {
            $this->methods = array_merge((isset($this->methods) && $this->methods!=null) ? $this->methods : [], $methods);
        }

        return $this;
    }

    /**
     * Specifies the subset of methods to mock, requiring each to exist in the class.
     *
     * @param string[] $methods
     *
     * @throws CannotUseOnlyMethodsException
     * @throws ReflectionException
     *
     * @return $this
     */
    public function onlyMethods(array $methods)
    {
        if (empty($methods)) {
            $this->emptyMethodsArray = true;

            return $this;
        }

        try {
            $reflector = new ReflectionClass($this->type);
            // @codeCoverageIgnoreStart
        } catch (\ReflectionException $e) {
            throw new ReflectionException(
                $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
        // @codeCoverageIgnoreEnd

        foreach ($methods as $method) {
            if (!$reflector->hasMethod($method)) {
                throw new CannotUseOnlyMethodsException($this->type, $method);
            }
        }

        $this->methods = array_merge((isset($this->methods) && $this->methods!=null) ? $this->methods : [], $methods);

        return $this;
    }

    /**
     * Specifies methods that don't exist in the class which you want to mock.
     *
     * @param string[] $methods
     *
     * @throws CannotUseAddMethodsException
     * @throws ReflectionException
     * @throws RuntimeException
     *
     * @return $this
     */
    public function addMethods(array $methods)
    {
        if (empty($methods)) {
            $this->emptyMethodsArray = true;

            return $this;
        }

        try {
            $reflector = new ReflectionClass($this->type);
            // @codeCoverageIgnoreStart
        } catch (\ReflectionException $e) {
            throw new ReflectionException(
                $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
        // @codeCoverageIgnoreEnd

        foreach ($methods as $method) {
            if ($reflector->hasMethod($method)) {
                throw new CannotUseAddMethodsException($this->type, $method);
            }
        }

        $this->methods = array_merge((isset($this->methods) && $this->methods!=null) ? $this->methods : [], $methods);

        return $this;
    }

    /**
     * Specifies the subset of methods to not mock. Default is to mock all of them.
     *
     * @throws ReflectionException
     */
    public function setMethodsExcept(array $methods = [])
    {
        return $this->setMethods(
            array_diff(
                $this->generator->getClassMethods($this->type),
                $methods
            )
        );
    }

    /**
     * Specifies the arguments for the constructor.
     *
     * @return $this
     */
    public function setConstructorArgs(array $args)
    {
        $this->constructorArgs = $args;

        return $this;
    }

    /**
     * Specifies the name for the mock class.
     *
     * @return $this
     */
    public function setMockClassName($name)
    {
        $this->mockClassName = $name;

        return $this;
    }

    /**
     * Disables the invocation of the original constructor.
     *
     * @return $this
     */
    public function disableOriginalConstructor()
    {
        $this->originalConstructor = false;

        return $this;
    }

    /**
     * Enables the invocation of the original constructor.
     *
     * @return $this
     */
    public function enableOriginalConstructor()
    {
        $this->originalConstructor = true;

        return $this;
    }

    /**
     * Disables the invocation of the original clone constructor.
     *
     * @return $this
     */
    public function disableOriginalClone()
    {
        $this->originalClone = false;

        return $this;
    }

    /**
     * Enables the invocation of the original clone constructor.
     *
     * @return $this
     */
    public function enableOriginalClone()
    {
        $this->originalClone = true;

        return $this;
    }

    /**
     * Disables the use of class autoloading while creating the mock object.
     *
     * @return $this
     */
    public function disableAutoload()
    {
        $this->autoload = false;

        return $this;
    }

    /**
     * Enables the use of class autoloading while creating the mock object.
     *
     * @return $this
     */
    public function enableAutoload()
    {
        $this->autoload = true;

        return $this;
    }

    /**
     * Disables the cloning of arguments passed to mocked methods.
     *
     * @return $this
     */
    public function disableArgumentCloning()
    {
        $this->cloneArguments = false;

        return $this;
    }

    /**
     * Enables the cloning of arguments passed to mocked methods.
     *
     * @return $this
     */
    public function enableArgumentCloning()
    {
        $this->cloneArguments = true;

        return $this;
    }

    /**
     * Enables the invocation of the original methods.
     *
     * @return $this
     */
    public function enableProxyingToOriginalMethods()
    {
        $this->callOriginalMethods = true;

        return $this;
    }

    /**
     * Disables the invocation of the original methods.
     *
     * @return $this
     */
    public function disableProxyingToOriginalMethods()
    {
        $this->callOriginalMethods = false;
        $this->proxyTarget         = null;

        return $this;
    }

    /**
     * Sets the proxy target.
     *
     * @return $this
     */
    public function setProxyTarget(object $object)
    {
        $this->proxyTarget = $object;

        return $this;
    }

    /**
     * @return $this
     */
    public function allowMockingUnknownTypes()
    {
        $this->allowMockingUnknownTypes = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function disallowMockingUnknownTypes()
    {
        $this->allowMockingUnknownTypes = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function enableAutoReturnValueGeneration()
    {
        $this->returnValueGeneration = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableAutoReturnValueGeneration()
    {
        $this->returnValueGeneration = false;

        return $this;
    }
}
