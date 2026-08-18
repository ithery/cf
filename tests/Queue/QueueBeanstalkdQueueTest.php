<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Pheanstalk\Job as PheanstalkJob;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

/**
 * Driver antrean beanstalkd.
 *
 * Diuji lewat klien Pheanstalk tiruan, bukan peladen sungguhan: yang diperiksa
 * **bentuk perintah yang dikirim** - tube mana yang dipakai, prioritas, jeda, dan
 * time-to-run - bukan perilaku beanstalkd itu sendiri.
 *
 * Dua hal yang paling layak dijaga ketat, dan keduanya gagal tanpa suara:
 *
 *  - **Pemilihan tube.** Job yang masuk ke tube yang salah tetap "berhasil"
 *    dikirim; tidak ada galat, tidak ada job gagal. Ia hanya menunggu di tempat
 *    yang tidak ditonton pekerja mana pun.
 *  - **Perhitungan jeda pada `later()`.** Salah satuan atau salah acuan waktu
 *    menghasilkan job yang dijadwalkan jauh di masa depan - juga tanpa gejala,
 *    sampai ada yang menyadari pesannya tidak pernah sampai.
 *
 * Driver ini sempat dianggap tak dapat diuji karena Pheanstalk disangka tidak
 * ada. Pustakanya ada; sejak 16 Agustus 2026 ia di `system/vendor/`.
 */
class QueueBeanstalkdQueueTest extends TestCase {
    use MockeryPHPUnitIntegration;

    /**
     * @var int
     */
    const TIME_TO_RUN = 60;

    /**
     * @return \Mockery\MockInterface
     */
    protected function makePheanstalk() {
        return m::mock('Pheanstalk\Pheanstalk');
    }

    /**
     * @param \Mockery\MockInterface $pheanstalk
     * @param int                    $blockFor
     *
     * @return CQueue_Queue_BeanstalkdQueue
     */
    protected function makeQueue($pheanstalk, $blockFor = 0) {
        $queue = new CQueue_Queue_BeanstalkdQueue($pheanstalk, 'default', static::TIME_TO_RUN, $blockFor);
        $queue->setContainer(CContainer::getInstance());
        $queue->setConnectionName('beanstalkd');

        return $queue;
    }

    /**
     * Tube default dipakai kalau tidak disebut. Nilai kosong pun jatuh ke default
     * karena penyaringnya `?:`, bukan `??` - string kosong tidak boleh menjadi
     * nama tube.
     *
     * @return void
     */
    public function testTheDefaultTubeIsUsedWhenNoneIsGiven() {
        $queue = $this->makeQueue($this->makePheanstalk());

        $this->assertSame('default', $queue->getQueue(null));
        $this->assertSame('default', $queue->getQueue(''));
        $this->assertSame('lainnya', $queue->getQueue('lainnya'));
    }

    /**
     * @return void
     */
    public function testSizeReadsReadyJobsOfTheTube() {
        $pheanstalk = $this->makePheanstalk();
        $stats = (object) ['current_jobs_ready' => '7'];
        $pheanstalk->shouldReceive('statsTube')->once()->with('default')->andReturn($stats);

        $this->assertSame(7, $this->makeQueue($pheanstalk)->size());
    }

    /**
     * @return void
     */
    public function testSizeUsesTheGivenTube() {
        $pheanstalk = $this->makePheanstalk();
        $pheanstalk->shouldReceive('statsTube')->once()->with('lainnya')
            ->andReturn((object) ['current_jobs_ready' => 3]);

        $this->assertSame(3, $this->makeQueue($pheanstalk)->size('lainnya'));
    }

    /**
     * Prioritas, jeda, dan time-to-run yang dikirim ke `put()`. Time-to-run
     * datang dari konfigurasi (`retry_after`), bukan dari bawaan Pheanstalk -
     * salah di sini membuat job dianggap mati terlalu cepat lalu dikerjakan dua
     * kali.
     *
     * @return void
     */
    public function testPushRawPutsWithTheConfiguredTimeToRun() {
        $pheanstalk = $this->makePheanstalk();
        $pheanstalk->shouldReceive('useTube')->once()->with('default')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('put')->once()->with(
            'muatan-mentah',
            Pheanstalk\Pheanstalk::DEFAULT_PRIORITY,
            Pheanstalk\Pheanstalk::DEFAULT_DELAY,
            static::TIME_TO_RUN
        )->andReturn(new PheanstalkJob(11, 'muatan-mentah'));

        $this->makeQueue($pheanstalk)->pushRaw('muatan-mentah');
    }

    /**
     * @return void
     */
    public function testPushRawUsesTheGivenTube() {
        $pheanstalk = $this->makePheanstalk();
        $pheanstalk->shouldReceive('useTube')->once()->with('lainnya')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('put')->once()->andReturn(new PheanstalkJob(12, ''));

        $this->makeQueue($pheanstalk)->pushRaw('muatan', 'lainnya');
    }

    /**
     * Perilaku yang berlaku sekarang, ditulis apa adanya: **`$options`
     * diabaikan**. Driver lain memakainya untuk prioritas atau penundaan, jadi
     * pemanggil yang mengoper `['priority' => ...]` ke sini akan mengira ia
     * berpengaruh padahal tidak.
     *
     * Test ini mengunci keadaan sekarang, bukan menyatakan itu benar. Kalau suatu
     * saat opsi mulai dihormati, test inilah yang perlu ikut diubah.
     *
     * @return void
     */
    public function testPushRawIgnoresTheOptionsArray() {
        $pheanstalk = $this->makePheanstalk();
        $pheanstalk->shouldReceive('useTube')->once()->with('default')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('put')->once()->with(
            'muatan',
            Pheanstalk\Pheanstalk::DEFAULT_PRIORITY,
            Pheanstalk\Pheanstalk::DEFAULT_DELAY,
            static::TIME_TO_RUN
        )->andReturn(new PheanstalkJob(13, 'muatan'));

        $this->makeQueue($pheanstalk)->pushRaw('muatan', null, ['priority' => 5, 'delay' => 99]);
    }

    /**
     * Muatan yang dibangun `push()` harus memuat nama job dan datanya, dan
     * menyebut tube tujuannya.
     *
     * @return void
     */
    public function testPushBuildsAJsonPayloadNamingTheJobAndQueue() {
        $pheanstalk = $this->makePheanstalk();
        $pheanstalk->shouldReceive('useTube')->once()->with('default')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('put')->once()->with(
            m::on(function ($payload) {
                $decoded = json_decode($payload, true);

                return is_array($decoded)
                    && carr::get($decoded, 'job') === 'PekerjaanUji'
                    && carr::get($decoded, 'data.satu') === 1;
            }),
            Pheanstalk\Pheanstalk::DEFAULT_PRIORITY,
            Pheanstalk\Pheanstalk::DEFAULT_DELAY,
            static::TIME_TO_RUN
        )->andReturn(new PheanstalkJob(14, ''));

        $this->makeQueue($pheanstalk)->push('PekerjaanUji', ['satu' => 1]);
    }

    /**
     * Jeda dalam detik diteruskan apa adanya sebagai argumen delay.
     *
     * @return void
     */
    public function testLaterPassesAnIntegerDelayStraightThrough() {
        $pheanstalk = $this->makePheanstalk();
        $pheanstalk->shouldReceive('useTube')->once()->with('default')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('put')->once()->with(
            m::any(),
            Pheanstalk\Pheanstalk::DEFAULT_PRIORITY,
            120,
            static::TIME_TO_RUN
        )->andReturn(new PheanstalkJob(15, ''));

        $this->makeQueue($pheanstalk)->later(120, 'PekerjaanUji');
    }

    /**
     * Dan waktu absolut diterjemahkan menjadi selisih detik dari sekarang.
     * Ini bagian yang paling mudah salah dan paling sunyi kalau salah.
     *
     * @return void
     */
    public function testLaterTranslatesADateIntoSecondsFromNow() {
        $pheanstalk = $this->makePheanstalk();
        $pheanstalk->shouldReceive('useTube')->once()->with('default')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('put')->once()->with(
            m::any(),
            Pheanstalk\Pheanstalk::DEFAULT_PRIORITY,
            //beberapa detik berlalu antara penyusunan dan pemanggilan
            m::on(function ($delay) {
                return is_int($delay) && $delay > 290 && $delay <= 300;
            }),
            static::TIME_TO_RUN
        )->andReturn(new PheanstalkJob(16, ''));

        $this->makeQueue($pheanstalk)->later(c::now()->addSeconds(300), 'PekerjaanUji');
    }

    /**
     * @return void
     */
    public function testLaterUsesTheGivenTube() {
        $pheanstalk = $this->makePheanstalk();
        $pheanstalk->shouldReceive('useTube')->with('lainnya')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('put')->once()->andReturn(new PheanstalkJob(17, ''));

        $this->makeQueue($pheanstalk)->later(10, 'PekerjaanUji', '', 'lainnya');
    }

    /**
     * `bulk` mendorong tiap job satu per satu - bukan satu muatan gabungan.
     *
     * @return void
     */
    public function testBulkPushesEveryJobSeparately() {
        $pheanstalk = $this->makePheanstalk();
        $pheanstalk->shouldReceive('useTube')->times(3)->with('default')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('put')->times(3)->andReturn(new PheanstalkJob(18, ''));

        $this->makeQueue($pheanstalk)->bulk(['Satu', 'Dua', 'Tiga']);
    }

    /**
     * Job yang membawa `delay` lewat jalur `later()`, jadi jedanya bukan nol.
     *
     * @return void
     */
    public function testBulkRoutesADelayedJobThroughLater() {
        $pheanstalk = $this->makePheanstalk();
        $pheanstalk->shouldReceive('useTube')->with('default')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('put')->once()->with(
            m::any(),
            Pheanstalk\Pheanstalk::DEFAULT_PRIORITY,
            45,
            static::TIME_TO_RUN
        )->andReturn(new PheanstalkJob(19, ''));

        $tertunda = new stdClass();
        $tertunda->delay = 45;

        $this->makeQueue($pheanstalk)->bulk([$tertunda]);
    }

    /**
     * `watchOnly` yang menentukan tube mana yang ditonton - `watch` biasa akan
     * menambah tube tanpa melepas yang lama, sehingga pekerja diam-diam ikut
     * mengambil job milik antrean lain.
     *
     * @return void
     */
    public function testPopWatchesOnlyTheRequestedTube() {
        $pheanstalk = $this->makePheanstalk();
        $pheanstalk->shouldReceive('watchOnly')->once()->with('lainnya')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('reserveWithTimeout')->once()->with(0)->andReturn(null);

        $this->assertNull($this->makeQueue($pheanstalk)->pop('lainnya'));
    }

    /**
     * @return void
     */
    public function testPopPassesTheBlockForTimeout() {
        $pheanstalk = $this->makePheanstalk();
        $pheanstalk->shouldReceive('watchOnly')->once()->with('default')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('reserveWithTimeout')->once()->with(5)->andReturn(null);

        $this->makeQueue($pheanstalk, 5)->pop();
    }

    /**
     * @return void
     */
    public function testPopWrapsAReservedJob() {
        $pheanstalk = $this->makePheanstalk();
        $pheanstalk->shouldReceive('watchOnly')->once()->with('default')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('reserveWithTimeout')->once()->andReturn(new PheanstalkJob(21, 'muatan'));

        $job = $this->makeQueue($pheanstalk)->pop();

        $this->assertInstanceOf('CQueue_Job_BeanstalkdJob', $job);
    }

    /**
     * Tidak ada job berarti null, bukan objek kosong - pekerja membedakan
     * keduanya untuk memutuskan tidur.
     *
     * @return void
     */
    public function testPopReturnsNullWhenNothingIsReserved() {
        $pheanstalk = $this->makePheanstalk();
        $pheanstalk->shouldReceive('watchOnly')->once()->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('reserveWithTimeout')->once()->andReturn(false);

        $this->assertNull($this->makeQueue($pheanstalk)->pop());
    }

    /**
     * @return void
     */
    public function testDeleteMessageDeletesFromTheRightTube() {
        $pheanstalk = $this->makePheanstalk();
        $pheanstalk->shouldReceive('useTube')->once()->with('lainnya')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('delete')->once()->with(m::on(function ($job) {
            return $job instanceof PheanstalkJob && $job->getId() == 99;
        }));

        $this->makeQueue($pheanstalk)->deleteMessage('lainnya', 99);
    }

    /**
     * @return void
     */
    public function testItExposesTheUnderlyingClient() {
        $pheanstalk = $this->makePheanstalk();

        $this->assertSame($pheanstalk, $this->makeQueue($pheanstalk)->getPheanstalk());
    }
}
