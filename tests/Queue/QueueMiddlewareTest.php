<?php

use PHPUnit\Framework\TestCase;

class QueueMiddlewareTestJob {
    /**
     * @var array
     */
    public $released = [];

    /**
     * @param int $delay
     *
     * @return void
     */
    public function release($delay = 0) {
        $this->released[] = $delay;
    }
}

class QueueMiddlewareTestOtherJob extends QueueMiddlewareTestJob {
}

class QueueMiddlewareTest extends TestCase {
    /**
     * @var string
     */
    protected $unik;

    protected function setUp() {
        //kunci dan nama pembatas dibuat unik per test alih-alih menyiram cache:
        //driver bawaannya file, dan flush() menghapus direktorinya sehingga
        //penulisan sesudahnya gagal (sudah dicatat di TODO framework)
        $this->unik = uniqid('uji', true);
    }

    /**
     * @param string $nama
     *
     * @return string
     */
    protected function kunci($nama) {
        return $nama . '-' . $this->unik;
    }

    /**
     * @return callable
     */
    protected function counter(&$dijalankan) {
        return function ($job) use (&$dijalankan) {
            $dijalankan++;
        };
    }

    public function testTheJobRunsWhenTheLockIsFree() {
        $dijalankan = 0;
        $job = new QueueMiddlewareTestJob();

        (new CQueue_Middleware_WithoutOverlapping($this->kunci('kunci')))->handle($job, $this->counter($dijalankan));

        $this->assertSame(1, $dijalankan);
        $this->assertSame([], $job->released);
    }

    /**
     * Kuncinya dilepas lagi sesudah job selesai, jadi dua job berurutan -- bukan
     * bersamaan -- tetap keduanya jalan.
     */
    public function testTheLockIsReleasedAfterTheJobFinishes() {
        $dijalankan = 0;
        $middleware = new CQueue_Middleware_WithoutOverlapping($this->kunci('kunci'));

        $middleware->handle(new QueueMiddlewareTestJob(), $this->counter($dijalankan));
        $middleware->handle(new QueueMiddlewareTestJob(), $this->counter($dijalankan));

        $this->assertSame(2, $dijalankan);
    }

    /**
     * Job kedua yang datang saat yang pertama masih berjalan tidak dijalankan,
     * melainkan dikembalikan ke antrean -- itulah gunanya middleware ini.
     */
    public function testASecondJobIsReleasedWhileTheFirstStillHoldsTheLock() {
        $dalam = 0;
        $luar = new QueueMiddlewareTestJob();
        $dalamJob = new QueueMiddlewareTestJob();

        (new CQueue_Middleware_WithoutOverlapping($this->kunci('kunci')))->handle($luar, function () use (&$dalam, $dalamJob) {
            (new CQueue_Middleware_WithoutOverlapping($this->kunci('kunci')))->releaseAfter(30)->handle($dalamJob, function () use (&$dalam) {
                $dalam++;
            });
        });

        $this->assertSame(0, $dalam);
        $this->assertSame([30], $dalamJob->released);
    }

    public function testDontReleaseSimplyDropsTheOverlappingJob() {
        $dalamJob = new QueueMiddlewareTestJob();

        (new CQueue_Middleware_WithoutOverlapping($this->kunci('kunci')))->handle(new QueueMiddlewareTestJob(), function () use ($dalamJob) {
            (new CQueue_Middleware_WithoutOverlapping($this->kunci('kunci')))->dontRelease()->handle($dalamJob, function () {
            });
        });

        $this->assertSame([], $dalamJob->released);
    }

    /**
     * Kunci yang gagal dilepas karena job-nya melempar tetap harus terlepas,
     * kalau tidak satu kegagalan menghalangi seluruh job berikutnya.
     */
    public function testTheLockIsReleasedEvenWhenTheJobThrows() {
        $middleware = new CQueue_Middleware_WithoutOverlapping($this->kunci('kunci'));

        try {
            $middleware->handle(new QueueMiddlewareTestJob(), function () {
                throw new RuntimeException('meledak');
            });
            $this->fail('seharusnya melempar');
        } catch (RuntimeException $e) {
            //diabaikan, yang diuji keadaan kuncinya sesudah ini
        }

        $dijalankan = 0;
        $middleware->handle(new QueueMiddlewareTestJob(), $this->counter($dijalankan));

        $this->assertSame(1, $dijalankan);
    }

    /**
     * Kunci berbeda tidak saling menghalangi -- misalnya satu job per pelanggan.
     */
    public function testTwoDifferentKeysDoNotBlockEachOther() {
        $dalam = 0;

        (new CQueue_Middleware_WithoutOverlapping($this->kunci('pelanggan-1')))->handle(new QueueMiddlewareTestJob(), function () use (&$dalam) {
            (new CQueue_Middleware_WithoutOverlapping($this->kunci('pelanggan-2')))->handle(new QueueMiddlewareTestJob(), function () use (&$dalam) {
                $dalam++;
            });
        });

        $this->assertSame(1, $dalam);
    }

    /**
     * Kunci bawaannya mengandung nama kelas job, sehingga dua jenis job dengan
     * kunci yang sama tetap boleh berjalan bersamaan. shared() mematikan itu.
     */
    public function testTheLockKeyIsScopedToTheJobClassByDefault() {
        $middleware = new CQueue_Middleware_WithoutOverlapping($this->kunci('kunci'));

        $this->assertSame(
            'cf-queue-overlap:QueueMiddlewareTestJob:' . $this->kunci('kunci'),
            $middleware->getLockKey(new QueueMiddlewareTestJob())
        );
        $this->assertNotSame(
            $middleware->getLockKey(new QueueMiddlewareTestJob()),
            $middleware->getLockKey(new QueueMiddlewareTestOtherJob())
        );
    }

    public function testSharedDropsTheJobClassFromTheKey() {
        $middleware = (new CQueue_Middleware_WithoutOverlapping($this->kunci('kunci')))->shared();

        $this->assertSame('cf-queue-overlap:' . $this->kunci('kunci'), $middleware->getLockKey(new QueueMiddlewareTestJob()));
        $this->assertSame(
            $middleware->getLockKey(new QueueMiddlewareTestJob()),
            $middleware->getLockKey(new QueueMiddlewareTestOtherJob())
        );
    }

    public function testWithPrefixReplacesTheKeyPrefix() {
        $middleware = (new CQueue_Middleware_WithoutOverlapping($this->kunci('kunci')))->withPrefix('lain:');

        $this->assertSame('lain:QueueMiddlewareTestJob:' . $this->kunci('kunci'), $middleware->getLockKey(new QueueMiddlewareTestJob()));
    }

    public function testTheFluentSettersReturnTheMiddleware() {
        $middleware = new CQueue_Middleware_WithoutOverlapping($this->kunci('kunci'));

        $this->assertSame($middleware, $middleware->releaseAfter(5));
        $this->assertSame($middleware, $middleware->dontRelease());
        $this->assertSame($middleware, $middleware->expireAfter(5));
        $this->assertSame($middleware, $middleware->withPrefix('lain:'));
        $this->assertSame($middleware, $middleware->shared());
    }

    public function testExpireAfterIsKeptInSeconds() {
        $middleware = (new CQueue_Middleware_WithoutOverlapping($this->kunci('kunci')))->expireAfter(120);

        $this->assertSame(120, $middleware->expiresAfter);
    }

    /**
     * Pembatas yang tidak terdaftar berarti tidak ada batas: job-nya lewat
     * begitu saja alih-alih ditolak.
     */
    public function testAnUnknownRateLimiterLetsTheJobThrough() {
        $dijalankan = 0;

        (new CQueue_Middleware_RateLimited($this->kunci('tidak-terdaftar')))->handle(new QueueMiddlewareTestJob(), $this->counter($dijalankan));

        $this->assertSame(1, $dijalankan);
    }

    public function testAnUnlimitedLimiterLetsEveryJobThrough() {
        CCache::rateLimiter()->aliasFor($this->kunci('bebas'), function () {
            return CCache_RateLimiting_Limit::none();
        });

        $dijalankan = 0;
        for ($i = 0; $i < 5; $i++) {
            (new CQueue_Middleware_RateLimited($this->kunci('bebas')))->handle(new QueueMiddlewareTestJob(), $this->counter($dijalankan));
        }

        $this->assertSame(5, $dijalankan);
    }

    public function testJobsRunUntilTheLimitIsReached() {
        CCache::rateLimiter()->aliasFor($this->kunci('terbatas'), function () {
            return CCache_RateLimiting_Limit::perMinute(2)->by($this->unik);
        });

        $dijalankan = 0;
        $job = new QueueMiddlewareTestJob();

        for ($i = 0; $i < 3; $i++) {
            (new CQueue_Middleware_RateLimited($this->kunci('terbatas')))->handle($job, $this->counter($dijalankan));
        }

        $this->assertSame(2, $dijalankan);
        $this->assertCount(1, $job->released);
    }

    /**
     * Job yang tertahan batas dikembalikan dengan jeda sampai jendelanya
     * terbuka, bukan langsung -- kalau langsung, ia akan berputar tanpa henti.
     */
    public function testAThrottledJobIsReleasedWithADelay() {
        CCache::rateLimiter()->aliasFor($this->kunci('terbatas'), function () {
            return CCache_RateLimiting_Limit::perMinute(1)->by($this->unik);
        });

        $job = new QueueMiddlewareTestJob();
        $dijalankan = 0;

        (new CQueue_Middleware_RateLimited($this->kunci('terbatas')))->handle($job, $this->counter($dijalankan));
        (new CQueue_Middleware_RateLimited($this->kunci('terbatas')))->handle($job, $this->counter($dijalankan));

        $this->assertSame(1, $dijalankan);
        $this->assertGreaterThan(0, $job->released[0]);
    }

    public function testDontReleaseDropsTheThrottledJobInstead() {
        CCache::rateLimiter()->aliasFor($this->kunci('terbatas'), function () {
            return CCache_RateLimiting_Limit::perMinute(1)->by($this->unik);
        });

        $job = new QueueMiddlewareTestJob();
        $dijalankan = 0;

        (new CQueue_Middleware_RateLimited($this->kunci('terbatas')))->handle($job, $this->counter($dijalankan));
        $hasil = (new CQueue_Middleware_RateLimited($this->kunci('terbatas')))->dontRelease()->handle($job, $this->counter($dijalankan));

        $this->assertSame(1, $dijalankan);
        $this->assertSame([], $job->released);
        $this->assertFalse($hasil);
    }

    /**
     * Middleware ini ikut serialisasi bersama job-nya, dan pembatasnya sendiri
     * tidak dapat diserialisasi -- hanya namanya yang dibawa lalu dipulihkan.
     */
    public function testOnlyTheLimiterNameSurvivesSerialization() {
        $middleware = (new CQueue_Middleware_RateLimited($this->kunci('terbatas')))->dontRelease();

        $this->assertSame(['limiterName', 'shouldRelease'], $middleware->__sleep());

        $dibangun = unserialize(serialize($middleware));

        $this->assertFalse($dibangun->shouldRelease);

        $dijalankan = 0;
        $dibangun->handle(new QueueMiddlewareTestJob(), $this->counter($dijalankan));

        $this->assertSame(1, $dijalankan);
    }
}
