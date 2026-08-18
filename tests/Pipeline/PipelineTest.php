<?php

use PHPUnit\Framework\TestCase;

class PipelineTestPipeOne {
    /**
     * @var array
     */
    public static $seen = [];

    /**
     * @param mixed    $passable
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($passable, $next) {
        static::$seen[] = $passable;

        return $next($passable . '-satu');
    }
}

class PipelineTestPipeTwo {
    /**
     * @param mixed    $passable
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($passable, $next) {
        return $next($passable . '-dua');
    }
}

class PipelineTestParameterPipe {
    /**
     * @param mixed    $passable
     * @param \Closure $next
     * @param mixed    $satu
     * @param mixed    $dua
     *
     * @return mixed
     */
    public function handle($passable, $next, $satu = null, $dua = null) {
        return $next($passable . ':' . $satu . ',' . $dua);
    }
}

class PipelineTestInvokablePipe {
    /**
     * @param mixed    $passable
     * @param \Closure $next
     *
     * @return mixed
     */
    public function __invoke($passable, $next) {
        return $next($passable . '-invokable');
    }
}

class PipelineTestCustomMethodPipe {
    /**
     * @param mixed    $passable
     * @param \Closure $next
     *
     * @return mixed
     */
    public function proses($passable, $next) {
        return $next($passable . '-custom');
    }
}

class PipelineTestThrowingPipe {
    /**
     * @param mixed    $passable
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($passable, $next) {
        throw new RuntimeException('meledak di pipa');
    }
}

class PipelineTest extends TestCase {
    protected function setUp() {
        PipelineTestPipeOne::$seen = [];
    }

    /**
     * @return CBase_Pipeline
     */
    protected function makePipeline() {
        return new CBase_Pipeline(CContainer::getInstance());
    }

    public function testWithoutPipesThePassableReachesTheDestinationUntouched() {
        $hasil = $this->makePipeline()->send('muatan')->through([])->then(function ($passable) {
            return $passable . '-selesai';
        });

        $this->assertSame('muatan-selesai', $hasil);
    }

    public function testAClosurePipeIsCalled() {
        $hasil = $this->makePipeline()->send('muatan')->through([
            function ($passable, $next) {
                return $next($passable . '-closure');
            },
        ])->thenReturn();

        $this->assertSame('muatan-closure', $hasil);
    }

    /**
     * Pipa dijalankan menurut urutan daftarnya, bukan terbalik: yang pertama
     * disebut adalah lapisan terluar.
     */
    public function testPipesRunInTheOrderTheyWereListed() {
        $hasil = $this->makePipeline()->send('muatan')->through([
            PipelineTestPipeOne::class,
            PipelineTestPipeTwo::class,
        ])->thenReturn();

        $this->assertSame('muatan-satu-dua', $hasil);
        $this->assertSame(['muatan'], PipelineTestPipeOne::$seen);
    }

    public function testAPipeGivenAsAnObjectIsUsedAsIs() {
        $hasil = $this->makePipeline()->send('muatan')->through([
            new PipelineTestPipeTwo(),
        ])->thenReturn();

        $this->assertSame('muatan-dua', $hasil);
    }

    public function testAnInvokablePipeIsCalled() {
        $hasil = $this->makePipeline()->send('muatan')->through([
            new PipelineTestInvokablePipe(),
        ])->thenReturn();

        $this->assertSame('muatan-invokable', $hasil);
    }

    /**
     * through() menerima daftar sebagai argumen terpisah maupun sebagai satu
     * larik, sehingga pemanggil tidak perlu membungkusnya.
     */
    public function testThroughAcceptsLooseArguments() {
        $hasil = $this->makePipeline()->send('muatan')->through(
            PipelineTestPipeOne::class,
            PipelineTestPipeTwo::class
        )->thenReturn();

        $this->assertSame('muatan-satu-dua', $hasil);
    }

    /**
     * Sebuah pipa boleh membawa argumennya sendiri lewat notasi titik dua,
     * itulah yang membuat satu kelas middleware dapat dipakai ulang dengan
     * setelan berbeda.
     */
    public function testAStringPipeCanCarryItsOwnParameters() {
        $hasil = $this->makePipeline()->send('muatan')->through([
            PipelineTestParameterPipe::class . ':a,b',
        ])->thenReturn();

        $this->assertSame('muatan:a,b', $hasil);
    }

    public function testViaChangesTheMethodNameThatIsCalled() {
        $hasil = $this->makePipeline()->send('muatan')->through([
            PipelineTestCustomMethodPipe::class,
        ])->via('proses')->thenReturn();

        $this->assertSame('muatan-custom', $hasil);
    }

    /**
     * with() menambahkan argumen untuk **semua** pipa, berbeda dari notasi
     * titik dua yang hanya berlaku bagi satu pipa.
     */
    public function testWithAppendsParametersForEveryPipe() {
        $hasil = $this->makePipeline()->send('muatan')->through([
            PipelineTestParameterPipe::class,
        ])->with('x', 'y')->thenReturn();

        $this->assertSame('muatan:x,y', $hasil);
    }

    public function testAPipeMayShortCircuitByNotCallingNext() {
        $hasil = $this->makePipeline()->send('muatan')->through([
            function ($passable, $next) {
                return 'dihentikan';
            },
            PipelineTestPipeTwo::class,
        ])->then(function ($passable) {
            return $passable . '-selesai';
        });

        $this->assertSame('dihentikan', $hasil);
    }

    public function testTheDestinationSeesWhatTheLastPipePassedOn() {
        $hasil = $this->makePipeline()->send('muatan')->through([
            PipelineTestPipeOne::class,
        ])->then(function ($passable) {
            return strtoupper($passable);
        });

        $this->assertSame('MUATAN-SATU', $hasil);
    }

    /**
     * Pengecualian dari sebuah pipa dilempar apa adanya; ia tidak diserap
     * menjadi nilai balik, agar kegagalan tidak lewat diam-diam.
     */
    public function testAnExceptionFromAPipeIsRethrown() {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('meledak di pipa');

        $this->makePipeline()->send('muatan')->through([
            PipelineTestThrowingPipe::class,
        ])->thenReturn();
    }

    public function testAnExceptionFromTheDestinationIsRethrown() {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('meledak di tujuan');

        $this->makePipeline()->send('muatan')->through([])->then(function () {
            throw new RuntimeException('meledak di tujuan');
        });
    }

    public function testAnObjectPassableIsCarriedThroughByReference() {
        $objek = new stdClass();
        $objek->hitung = 0;

        $hasil = $this->makePipeline()->send($objek)->through([
            function ($passable, $next) {
                $passable->hitung++;

                return $next($passable);
            },
            function ($passable, $next) {
                $passable->hitung++;

                return $next($passable);
            },
        ])->thenReturn();

        $this->assertSame($objek, $hasil);
        $this->assertSame(2, $objek->hitung);
    }

    public function testSendThroughViaAndWithAllReturnThePipelineForChaining() {
        $pipeline = $this->makePipeline();

        $this->assertSame($pipeline, $pipeline->send('muatan'));
        $this->assertSame($pipeline, $pipeline->through([]));
        $this->assertSame($pipeline, $pipeline->via('handle'));
        $this->assertSame($pipeline, $pipeline->with());
    }
}
