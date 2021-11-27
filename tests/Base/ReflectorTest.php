<?php
use PHPUnit\Framework\TestCase;

class ReflectorTest extends TestCase {
    public function testGetClassName() {
        $method = (new ReflectionClass(TestClass::class))->getMethod('withPeriod');

        $this->assertSame(CPeriod::class, CBase_Reflector::getParameterClassName($method->getParameters()[0]));
    }

    public function testEmptyClassName() {
        $method = (new ReflectionClass(TestClass::class))->getMethod('withNull');

        $this->assertNull(CBase_Reflector::getParameterClassName($method->getParameters()[0]));
    }

    public function testStringTypeName() {
        $method = (new ReflectionClass(CTesting_Fake_Base_BusFake::class))->getMethod('dispatchedAfterResponse');

        $this->assertNull(CBase_Reflector::getParameterClassName($method->getParameters()[0]));
    }

    public function testSelfClassName() {
        $method = (new ReflectionClass(CModel::class))->getMethod('newPivot');

        $this->assertSame(CModel::class, CBase_Reflector::getParameterClassName($method->getParameters()[0]));
    }

    public function testParentClassName() {
        $method = (new ReflectionClass(B::class))->getMethod('f');

        $this->assertSame(A::class, CBase_Reflector::getParameterClassName($method->getParameters()[0]));
    }

    /**
     * @requires PHP >= 8
     */
    public function testUnionTypeName() {
        $method = (new ReflectionClass(F::class))->getMethod('f');

        $this->assertNull(CBase_Reflector::getParameterClassName($method->getParameters()[0]));
    }

    public function testIsCallable() {
        $this->assertTrue(CBase_Reflector::isCallable(function () {
        }));
        $this->assertTrue(CBase_Reflector::isCallable([B::class, 'f']));
        $this->assertFalse(CBase_Reflector::isCallable([TestClassWithCall::class, 'f']));
        $this->assertTrue(CBase_Reflector::isCallable([new TestClassWithCall(), 'f']));
        $this->assertTrue(CBase_Reflector::isCallable([TestClassWithCallStatic::class, 'f']));
        $this->assertFalse(CBase_Reflector::isCallable([new TestClassWithCallStatic(), 'f']));
        $this->assertFalse(CBase_Reflector::isCallable([new TestClassWithCallStatic()]));
        $this->assertFalse(CBase_Reflector::isCallable(['TotallyMissingClass', 'foo']));
        $this->assertTrue(CBase_Reflector::isCallable(['TotallyMissingClass', 'foo'], true));
    }
}
// @codingStandardsIgnoreStart
class A {
}

class B extends A {
    public function f(parent $x) {
    }
}

if (PHP_MAJOR_VERSION >= 8) {
    eval('
namespace Illuminate\Tests\Support;
class F
{
    public function f(A|Model $x)
    {
        //
    }
}'
    );
}
class TestClass {
    public function withPeriod(CPeriod $period) {
    }

    public function withNull($period) {
    }
}
class TestClassWithCall {
    public function __call($method, $parameters) {
    }
}

class TestClassWithCallStatic {
    public static function __callStatic($method, $parameters) {
    }
}
