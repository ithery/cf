<?php

use PHPUnit\Framework\TestCase;

class SupportMacroableTestSubject {
    use CTrait_Macroable;

    /**
     * @var string
     */
    protected $rahasia = 'terlindung';

    /**
     * @return string
     */
    public function bawaan() {
        return 'bawaan';
    }
}

class SupportMacroableTestOtherSubject {
    use CTrait_Macroable;
}

class SupportMacroableTestMixin {
    /**
     * @return \Closure
     */
    public function sapa() {
        return function ($nama) {
            return 'halo ' . $nama;
        };
    }

    /**
     * @return \Closure
     */
    protected function pamit() {
        return function () {
            return 'sampai jumpa';
        };
    }
}

class SupportMacroableTest extends TestCase {
    protected function tearDown() {
        //makro tersimpan statis per kelas, jadi harus dikosongkan lagi
        $this->flushMacros(SupportMacroableTestSubject::class);
        $this->flushMacros(SupportMacroableTestOtherSubject::class);
    }

    /**
     * @param string $class
     *
     * @return void
     */
    protected function flushMacros($class) {
        $property = new ReflectionProperty($class, 'macros');
        $property->setAccessible(true);
        $property->setValue(null, []);
    }

    public function testAMacroBecomesAnInstanceMethod() {
        SupportMacroableTestSubject::macro('tambahan', function () {
            return 'dari makro';
        });

        $this->assertSame('dari makro', (new SupportMacroableTestSubject())->tambahan());
    }

    public function testAMacroBecomesAStaticMethodToo() {
        SupportMacroableTestSubject::macro('tambahan', function () {
            return 'dari makro';
        });

        $this->assertSame('dari makro', SupportMacroableTestSubject::tambahan());
    }

    public function testAMacroReceivesItsArguments() {
        SupportMacroableTestSubject::macro('jumlah', function ($a, $b) {
            return $a + $b;
        });

        $this->assertSame(5, (new SupportMacroableTestSubject())->jumlah(2, 3));
    }

    /**
     * Makro dijalankan terikat pada contohnya, jadi ia dapat menyentuh keadaan
     * terlindung -- itulah yang membuatnya terasa seperti method sungguhan.
     */
    public function testAMacroIsBoundToTheInstance() {
        SupportMacroableTestSubject::macro('bocorkan', function () {
            return $this->rahasia;
        });

        $this->assertSame('terlindung', (new SupportMacroableTestSubject())->bocorkan());
    }

    public function testHasMacroReportsRegistration() {
        $this->assertFalse(SupportMacroableTestSubject::hasMacro('tambahan'));

        SupportMacroableTestSubject::macro('tambahan', function () {
        });

        $this->assertTrue(SupportMacroableTestSubject::hasMacro('tambahan'));
    }

    /**
     * Tiap kelas punya daftar makronya sendiri; mendaftarkan pada satu kelas
     * tidak menyebar ke kelas lain yang memakai trait yang sama.
     */
    public function testMacrosAreNotSharedBetweenClasses() {
        SupportMacroableTestSubject::macro('tambahan', function () {
        });

        $this->assertFalse(SupportMacroableTestOtherSubject::hasMacro('tambahan'));
    }

    public function testAMacroDoesNotShadowARealMethod() {
        SupportMacroableTestSubject::macro('bawaan', function () {
            return 'dari makro';
        });

        $this->assertSame('bawaan', (new SupportMacroableTestSubject())->bawaan());
    }

    public function testAnUnknownMethodStillFailsLoudly() {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Method tidakAda does not exist.');

        (new SupportMacroableTestSubject())->tidakAda();
    }

    public function testAnUnknownStaticMethodAlsoFailsLoudly() {
        $this->expectException(BadMethodCallException::class);

        SupportMacroableTestSubject::tidakAda();
    }

    /**
     * mixin memasang seluruh method sebuah objek sekaligus, termasuk yang
     * terlindung -- itulah cara sebuah paket menambahkan sekelompok method.
     */
    public function testAMixinRegistersEveryMethodItOffers() {
        SupportMacroableTestSubject::mixin(new SupportMacroableTestMixin());
        $subject = new SupportMacroableTestSubject();

        $this->assertSame('halo Hery', $subject->sapa('Hery'));
        $this->assertSame('sampai jumpa', $subject->pamit());
    }

    public function testANonClosureMacroIsCalledAsIs() {
        SupportMacroableTestSubject::macro('besarkan', 'strtoupper');

        $this->assertSame('HALO', (new SupportMacroableTestSubject())->besarkan('halo'));
    }
}
