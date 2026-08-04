<?php

use PHPUnit\Framework\TestCase;

class ReflectsClosureTraitTestSubject {
    use CTrait_ReflectsClosureTrait;

    /**
     * @param \Closure $closure
     *
     * @return array
     */
    public function types(Closure $closure) {
        return $this->closureParameterTypes($closure);
    }

    /**
     * @param \Closure $closure
     *
     * @return string
     */
    public function firstType(Closure $closure) {
        return $this->firstClosureParameterType($closure);
    }
}

class ReflectsClosureTraitTest extends TestCase {
    /**
     * @return ReflectsClosureTraitTestSubject
     */
    protected function makeSubject() {
        return new ReflectsClosureTraitTestSubject();
    }

    public function testAClassTypeHintIsRead() {
        $types = $this->makeSubject()->types(function (RuntimeException $e) {
        });

        $this->assertSame(['e' => 'RuntimeException'], $types);
    }

    public function testFirstTypeReturnsTheClassName() {
        $this->assertSame('RuntimeException', $this->makeSubject()->firstType(function (RuntimeException $e) {
        }));
    }

    /**
     * Parameter tanpa type hint dijawab null, bukan fatal. Sebelumnya
     * ReflectionParameter::getClass() dipanggil apa adanya, sehingga sebuah
     * closure biasa -- misalnya yang dioper ke reportable() -- mematikan
     * permintaannya dengan "Call to a member function getName() on null".
     */
    public function testAnUntypedParameterIsReportedAsNull() {
        $types = $this->makeSubject()->types(function ($apa) {
        });

        $this->assertSame(['apa' => null], $types);
    }

    public function testABuiltinTypeIsReportedAsNullToo() {
        $types = $this->makeSubject()->types(function (string $teks) {
        });

        $this->assertSame(['teks' => null], $types);
    }

    public function testAVariadicParameterIsReportedAsNull() {
        $types = $this->makeSubject()->types(function (...$apa) {
        });

        $this->assertSame(['apa' => null], $types);
    }

    public function testSeveralParametersAreAllRead() {
        $types = $this->makeSubject()->types(function (RuntimeException $e, $lain, LogicException $l) {
        });

        $this->assertSame([
            'e' => 'RuntimeException',
            'lain' => null,
            'l' => 'LogicException',
        ], $types);
    }

    /**
     * Yang tanpa type hint memang tidak dapat dipakai untuk menentukan jenis,
     * jadi pemanggilnya diberi tahu dengan jelas -- bukan dibiarkan mati.
     */
    public function testAnUntypedFirstParameterIsRefusedWithAClearMessage() {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The first parameter of the given Closure is missing a type hint.');

        $this->makeSubject()->firstType(function ($apa) {
        });
    }

    public function testAClosureWithoutParametersIsRefused() {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The given Closure has no parameters.');

        $this->makeSubject()->firstType(function () {
        });
    }
}
