<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

/**
 * Driver redis diuji lewat factory tiruan, bukan server sungguhan: yang
 * diperiksa di sini bentuk perintah yang dikirim -- nama kunci, jumlah kunci
 * yang dioper ke EVAL, dan waktu tersedia -- bukan perilaku redis itu sendiri.
 */
class QueueRedisQueueTest extends TestCase {
    use MockeryPHPUnitIntegration;

    /**
     * @return \Mockery\MockInterface
     */
    protected function makeConnection() {
        return m::mock(CRedis_AbstractConnection::class);
    }

    /**
     * @param \Mockery\MockInterface $connection
     * @param null|int               $blockFor
     *
     * @return CQueue_Queue_RedisQueue
     */
    protected function makeQueue($connection, $blockFor = null) {
        $redis = m::mock(CRedis_FactoryInterface::class);
        $redis->shouldReceive('connection')->andReturn($connection);

        $queue = new CQueue_Queue_RedisQueue($redis, 'default', null, 60, $blockFor);
        $queue->setContainer(CContainer::getInstance());
        $queue->setConnectionName('redis');

        return $queue;
    }

    /**
     * Nama antrean selalu berawalan `queues:`, dan turunan `:delayed`,
     * `:reserved`, `:notify` dibangun dari nama itu -- salah satu saja meleset
     * dan job-nya hilang ke kunci yang tidak pernah dibaca siapa pun.
     */
    public function testTheQueueNameIsAlwaysPrefixed() {
        $queue = $this->makeQueue($this->makeConnection());

        $this->assertSame('queues:default', $queue->getQueue(null));
        $this->assertSame('queues:lainnya', $queue->getQueue('lainnya'));
    }

    public function testSizeCountsTheThreeKeysAtOnce() {
        $connection = $this->makeConnection();
        $connection->shouldReceive('eval')->once()->with(
            CQueue_LuaScripts::size(),
            3,
            'queues:default',
            'queues:default:delayed',
            'queues:default:reserved'
        )->andReturn(5);

        $this->assertSame(5, $this->makeQueue($connection)->size());
    }

    public function testPushRawSendsThePayloadAndNotifies() {
        $connection = $this->makeConnection();
        $connection->shouldReceive('eval')->once()->with(
            CQueue_LuaScripts::push(),
            2,
            'queues:default',
            'queues:default:notify',
            '{"id":"abc"}'
        );

        $this->assertSame('abc', $this->makeQueue($connection)->pushRaw('{"id":"abc"}'));
    }

    public function testPushCarriesTheJobNameAndItsData() {
        $connection = $this->makeConnection();
        $connection->shouldReceive('eval')->once()->with(
            CQueue_LuaScripts::push(),
            2,
            'queues:default',
            'queues:default:notify',
            m::on(function ($payload) {
                $decoded = json_decode($payload, true);

                return $decoded['job'] === 'PekerjaanUji'
                    && $decoded['data'] === ['id' => 1]
                    && $decoded['attempts'] === 0
                    && strlen($decoded['id']) === 32;
            })
        );

        $this->makeQueue($connection)->push('PekerjaanUji', ['id' => 1]);
    }

    public function testPushOnUsesTheNamedQueue() {
        $connection = $this->makeConnection();
        $connection->shouldReceive('eval')->once()->with(
            CQueue_LuaScripts::push(),
            2,
            'queues:lainnya',
            'queues:lainnya:notify',
            m::any()
        );

        $this->makeQueue($connection)->pushOn('lainnya', 'PekerjaanUji');
    }

    /**
     * Job bertunda tidak masuk daftar utama melainkan himpunan terurut
     * `:delayed`, dengan skor berupa waktu ia boleh dijalankan.
     */
    public function testLaterGoesIntoTheDelayedSetScoredByItsDueTime() {
        $connection = $this->makeConnection();
        $sekarang = CCarbon::now()->getTimestamp();
        $connection->shouldReceive('zadd')->once()->with(
            'queues:default:delayed',
            m::on(function ($score) use ($sekarang) {
                return $score >= $sekarang + 60 && $score <= $sekarang + 61;
            }),
            m::any()
        );

        $this->makeQueue($connection)->later(60, 'PekerjaanUji');
    }

    public function testPopReturnsNothingWhenTheQueueIsEmpty() {
        $connection = $this->makeConnection();
        $connection->shouldReceive('eval')->andReturn(null);

        $this->assertNull($this->makeQueue($connection)->pop());
    }

    public function testPopReturnsARedisJobWhenOneIsReserved() {
        $connection = $this->makeConnection();
        $payload = json_encode(['id' => 'abc', 'job' => 'PekerjaanUji', 'data' => [], 'attempts' => 0]);
        $connection->shouldReceive('eval')->andReturnUsing(function ($script) use ($payload) {
            //dua panggilan pertama memindahkan job yang jatuh tempo, yang ketiga mengambilnya
            return $script === CQueue_LuaScripts::pop() ? [$payload, $payload] : [];
        });

        $job = $this->makeQueue($connection)->pop();

        $this->assertInstanceOf(CQueue_Job_RedisJob::class, $job);
        $this->assertSame('redis', $job->getConnectionName());
        $this->assertSame('default', $job->getQueue());
        $this->assertSame($payload, $job->getRawBody());
    }

    /**
     * Sebelum mengambil, job bertunda dan reservasi yang kedaluwarsa dipindahkan
     * lebih dulu ke daftar utama -- itulah yang membuat job dari worker yang
     * mati kembali terambil.
     */
    public function testPopMigratesDelayedAndExpiredJobsFirst() {
        $connection = $this->makeConnection();
        $dipindahkan = [];
        $connection->shouldReceive('eval')->andReturnUsing(function () use (&$dipindahkan) {
            $args = func_get_args();
            if ($args[0] === CQueue_LuaScripts::migrateExpiredJobs()) {
                $dipindahkan[] = $args[2];
            }

            return [];
        });

        $this->makeQueue($connection)->pop();

        $this->assertSame(['queues:default:delayed', 'queues:default:reserved'], $dipindahkan);
    }

    public function testDeleteReservedRemovesTheReservedCopy() {
        $connection = $this->makeConnection();
        $connection->shouldReceive('zrem')->once()->with('queues:default:reserved', 'dipesan');

        $job = m::mock(CQueue_Job_RedisJob::class);
        $job->shouldReceive('getReservedJob')->andReturn('dipesan');

        $this->makeQueue($connection)->deleteReserved('default', $job);
    }

    public function testDeleteAndReleaseMovesTheJobBackWithADelay() {
        $connection = $this->makeConnection();
        $sekarang = CCarbon::now()->getTimestamp();
        $connection->shouldReceive('eval')->once()->with(
            CQueue_LuaScripts::release(),
            2,
            'queues:default:delayed',
            'queues:default:reserved',
            'dipesan',
            m::on(function ($availableAt) use ($sekarang) {
                return $availableAt >= $sekarang + 30 && $availableAt <= $sekarang + 31;
            })
        );

        $job = m::mock(CQueue_Job_RedisJob::class);
        $job->shouldReceive('getReservedJob')->andReturn('dipesan');

        $this->makeQueue($connection)->deleteAndRelease('default', $job, 30);
    }

    /**
     * clear() harus menyentuh keempat kunci; menyisakan salah satunya berarti
     * job yang sudah dibuang muncul lagi.
     */
    public function testClearRemovesAllFourKeys() {
        $connection = $this->makeConnection();
        $connection->shouldReceive('eval')->once()->with(
            CQueue_LuaScripts::clear(),
            4,
            'queues:default',
            'queues:default:delayed',
            'queues:default:reserved',
            'queues:default:notify'
        )->andReturn(3);

        $this->assertSame(3, $this->makeQueue($connection)->clear(null));
    }

    public function testGetRedisHandsBackTheFactory() {
        $redis = m::mock(CRedis_FactoryInterface::class);
        $queue = new CQueue_Queue_RedisQueue($redis);

        $this->assertSame($redis, $queue->getRedis());
    }
}
