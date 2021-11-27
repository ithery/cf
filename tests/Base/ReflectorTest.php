<?php
use PHPUnit\Framework\TestCase;

class ReflectorTest extends TestCase {
    public function testGetClassName() {
        $method = (new ReflectionClass(PendingMailFake::class))->getMethod('send');

        $this->assertSame(Mailable::class, CBase_Reflector::getParameterClassName($method->getParameters()[0]));
    }

    public function testEmptyClassName() {
        $method = (new ReflectionClass(MailFake::class))->getMethod('assertSent');

        $this->assertNull(CBase_Reflector::getParameterClassName($method->getParameters()[0]));
    }

    public function testStringTypeName() {
        $method = (new ReflectionClass(BusFake::class))->getMethod('dispatchedAfterResponse');

        $this->assertNull(CBase_Reflector::getParameterClassName($method->getParameters()[0]));
    }

    public function testSelfClassName() {
        $method = (new ReflectionClass(Model::class))->getMethod('newPivot');

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
        $method = (new ReflectionClass(C::class))->getMethod('f');

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
class C
{
    public function f(A|Model $x)
    {
        //
    }
}'
    );
}

class TestClassWithCall {
    public function __call($method, $parameters) {
    }
}

class TestClassWithCallStatic {
    public static function __callStatic($method, $parameters) {
    }
}
