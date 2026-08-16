<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

/**
 * Penanda awal iterasi SCAN yang dikirim ke phpredis.
 *
 * phpredis memakai null untuk "mulai dari awal" dan 0 untuk "sudah selesai".
 * Meneruskan 0 apa adanya membuat phpredis langsung mengembalikan false, jadi
 * keempat pembungkus scan di sini tidak pernah mengembalikan satu pun kunci -
 * dan pemanggilnya membaca itu sebagai "tidak ada apa-apa", bukan sebagai galat.
 *
 * Diuji lewat klien tiruan karena yang diperiksa memang bentuk panggilannya,
 * bukan perilaku redis.
 */
class PhpRedisScanCursorTest extends TestCase {
    use MockeryPHPUnitIntegration;

    /**
     * @return void
     */
    public function testScanStartsTheIterationWithNull() {
        $client = m::mock('stdClass');
        $client->shouldReceive('scan')
            ->once()
            ->with(null, '*', 10)
            ->andReturn(['kunci:1']);

        $connection = new CRedis_Connection_PhpRedisConnection($client);

        $this->assertSame([0, ['kunci:1']], $connection->scan(0));
    }

    /**
     * @return void
     */
    public function testScanPassesMatchAndCountThrough() {
        $client = m::mock('stdClass');
        $client->shouldReceive('scan')
            ->once()
            ->with(null, 'pola:*', 100)
            ->andReturn(['pola:1']);

        $connection = new CRedis_Connection_PhpRedisConnection($client);

        $connection->scan(0, ['match' => 'pola:*', 'count' => 100]);
    }

    /**
     * Cursor lanjutan diteruskan apa adanya - hanya nilai awal yang diterjemahkan.
     *
     * @return void
     */
    public function testAContinuationCursorIsPassedUnchanged() {
        $client = m::mock('stdClass');
        $client->shouldReceive('scan')
            ->once()
            ->with(17, '*', 10)
            ->andReturn(['kunci:2']);

        $connection = new CRedis_Connection_PhpRedisConnection($client);

        $connection->scan(17);
    }

    /**
     * Kontrak lama dipertahankan: habis tetap dilaporkan sebagai false.
     *
     * @return void
     */
    public function testAnExhaustedScanStillReportsFalse() {
        $client = m::mock('stdClass');
        $client->shouldReceive('scan')->once()->andReturn(false);

        $connection = new CRedis_Connection_PhpRedisConnection($client);

        $this->assertFalse($connection->scan(0));
    }

    /**
     * @return void
     */
    public function testZscanStartsTheIterationWithNull() {
        $client = m::mock('stdClass');
        $client->shouldReceive('zscan')
            ->once()
            ->with('kunci', null, '*', 10)
            ->andReturn(['anggota' => 1]);

        $connection = new CRedis_Connection_PhpRedisConnection($client);

        $connection->zscan('kunci', 0);
    }

    /**
     * @return void
     */
    public function testHscanStartsTheIterationWithNull() {
        $client = m::mock('stdClass');
        $client->shouldReceive('hscan')
            ->once()
            ->with('kunci', null, '*', 10)
            ->andReturn(['ruas' => 'nilai']);

        $connection = new CRedis_Connection_PhpRedisConnection($client);

        $connection->hscan('kunci', 0);
    }

    /**
     * @return void
     */
    public function testSscanStartsTheIterationWithNull() {
        $client = m::mock('stdClass');
        $client->shouldReceive('sscan')
            ->once()
            ->with('kunci', null, '*', 10)
            ->andReturn(['anggota']);

        $connection = new CRedis_Connection_PhpRedisConnection($client);

        $connection->sscan('kunci', 0);
    }
}
