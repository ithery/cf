<?php

use PHPUnit\Framework\TestCase;

/**
 * Driver database diuji terhadap sqlite `:memory:` sungguhan, bukan tiruan.
 * Yang menentukan benar tidaknya driver ini adalah SQL-nya sendiri -- syarat
 * "tersedia atau reservasinya kedaluwarsa", penguncian saat pop, dan urutan
 * pengambilan. Query builder tiruan meloloskan semua itu tanpa pernah
 * menjalankannya.
 */
class QueueDatabaseQueueTest extends TestCase {
    /**
     * @var string
     */
    const TABLE = 'queue_job';

    /**
     * @var CDatabase_Connection_Pdo_SqliteConnection
     */
    protected $connection;

    protected function setUp() {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite tidak tersedia pada PHP CLI yang aktif.');
        }

        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->connection = new CDatabase_Connection_Pdo_SqliteConnection($pdo, '', '', ['driver' => 'sqlite']);
        $this->connection->setEventDispatcher(new CEvent_Dispatcher());

        $this->connection->getSchemaBuilder()->create(static::TABLE, function (CDatabase_Schema_Blueprint $table) {
            $table->increments(static::TABLE . '_id');
            $table->string('name');
            $table->string('app_code')->nullable();
            $table->integer('org_id')->nullable();
            $table->longText('payload');
            $table->unsignedInteger('attempts');
            $table->dateTime('reserved_at')->nullable();
            $table->dateTime('available_at');
            $table->dateTime('created')->nullable();
            $table->string('createdby')->nullable();
            $table->dateTime('updated')->nullable();
            $table->string('updatedby')->nullable();
        });
    }

    protected function tearDown() {
        CCarbon::setTestNow();
    }

    /**
     * @param int $retryAfter
     *
     * @return CQueue_Queue_DatabaseQueue
     */
    protected function makeQueue($retryAfter = 60) {
        $queue = new CQueue_Queue_DatabaseQueue($this->connection, static::TABLE, 'default', $retryAfter);
        $queue->setContainer(CContainer::getInstance());
        $queue->setConnectionName('database');

        return $queue;
    }

    /**
     * @return CDatabase_Query_Builder
     */
    protected function rows() {
        return $this->connection->table(static::TABLE);
    }

    public function testAnEmptyQueueHasNoSize() {
        $this->assertSame(0, $this->makeQueue()->size());
    }

    public function testPushStoresTheJobAndItsPayload() {
        $this->makeQueue()->push('PekerjaanUji', ['id' => 1]);

        $row = $this->rows()->first();
        $payload = json_decode($row->payload, true);

        $this->assertSame('default', $row->name);
        $this->assertSame(0, (int) $row->attempts);
        $this->assertNull($row->reserved_at);
        $this->assertSame('PekerjaanUji', $payload['job']);
        $this->assertSame(['id' => 1], $payload['data']);
    }

    public function testPushRawStoresTheBodyUntouched() {
        $this->makeQueue()->pushRaw('mentah');

        $this->assertSame('mentah', $this->rows()->first()->payload);
    }

    public function testSizeCountsPerQueueName() {
        $queue = $this->makeQueue();
        $queue->push('PekerjaanUji');
        $queue->pushOn('lainnya', 'PekerjaanUji');

        $this->assertSame(1, $queue->size());
        $this->assertSame(1, $queue->size('lainnya'));
        $this->assertSame(0, $queue->size('kosong'));
    }

    public function testBulkStoresEveryJobAtOnce() {
        $this->makeQueue()->bulk(['SatuJob', 'DuaJob', 'TigaJob'], ['id' => 1]);

        $this->assertSame(3, $this->rows()->count());
    }

    public function testPopReturnsADatabaseJob() {
        $queue = $this->makeQueue();
        $queue->push('PekerjaanUji', ['id' => 1]);

        $job = $queue->pop();

        $this->assertInstanceOf(CQueue_Job_DatabaseJob::class, $job);
        $this->assertSame('database', $job->getConnectionName());
        $this->assertSame('default', $job->getQueue());
    }

    /**
     * Pop menandai job sebagai dipesan dan menaikkan hitungan percobaan dalam
     * satu transaksi -- itulah yang mencegah dua worker mengambil job yang sama.
     */
    public function testPopReservesTheJobAndCountsTheAttempt() {
        $queue = $this->makeQueue();
        $queue->push('PekerjaanUji');
        $queue->pop();

        $row = $this->rows()->first();

        $this->assertNotNull($row->reserved_at);
        $this->assertSame(1, (int) $row->attempts);
    }

    public function testASecondPopFindsNothingWhileTheFirstIsStillReserved() {
        $queue = $this->makeQueue();
        $queue->push('PekerjaanUji');

        $this->assertNotNull($queue->pop());
        $this->assertNull($queue->pop());
    }

    public function testPopIgnoresJobsMeantForAnotherQueue() {
        $queue = $this->makeQueue();
        $queue->pushOn('lainnya', 'PekerjaanUji');

        $this->assertNull($queue->pop());
        $this->assertNotNull($queue->pop('lainnya'));
    }

    public function testJobsComeOutInTheOrderTheyWentIn() {
        $queue = $this->makeQueue();
        $queue->push('SatuJob');
        $queue->push('DuaJob');

        $this->assertSame('SatuJob', json_decode($queue->pop()->getRawBody(), true)['job']);
        $this->assertSame('DuaJob', json_decode($queue->pop()->getRawBody(), true)['job']);
    }

    /**
     * later() menyimpan waktu tersedia di masa depan, dan job itu tidak boleh
     * terambil sebelum waktunya tiba.
     */
    public function testADelayedJobIsNotAvailableYet() {
        $queue = $this->makeQueue();
        $queue->later(60, 'PekerjaanUji');

        $this->assertSame(1, $queue->size());
        $this->assertNull($queue->pop());
    }

    public function testADelayedJobBecomesAvailableWhenItsTimeComes() {
        $queue = $this->makeQueue();
        $queue->later(60, 'PekerjaanUji');

        CCarbon::setTestNow(CCarbon::now()->addSeconds(61));

        $this->assertNotNull($queue->pop());
    }

    /**
     * Reservasi yang lewat retryAfter dianggap ditinggalkan worker yang mati,
     * sehingga job-nya kembali dapat diambil. Tanpa ini, satu worker yang
     * terbunuh akan menyandera job-nya selamanya.
     */
    public function testAnExpiredReservationBecomesAvailableAgain() {
        $queue = $this->makeQueue(60);
        $queue->push('PekerjaanUji');
        $queue->pop();

        CCarbon::setTestNow(CCarbon::now()->addSeconds(61));

        $job = $queue->pop();

        $this->assertNotNull($job);
        $this->assertSame(2, $job->attempts());
    }

    public function testDeleteReservedRemovesTheRow() {
        $queue = $this->makeQueue();
        $queue->push('PekerjaanUji');
        $job = $queue->pop();

        $queue->deleteReserved('default', $job->getJobId());

        $this->assertSame(0, $this->rows()->count());
    }

    /**
     * release() menyimpan kembali job dengan hitungan percobaan yang terbawa,
     * bukan mengulang dari nol -- itulah yang membuat batas percobaan berarti.
     */
    public function testReleaseKeepsTheAttemptCount() {
        $queue = $this->makeQueue();
        $queue->push('PekerjaanUji');
        $job = $queue->pop();

        $queue->deleteAndRelease('default', $job, 0);

        $row = $this->rows()->first();

        $this->assertSame(1, $this->rows()->count());
        $this->assertSame(1, (int) $row->attempts);
        $this->assertNull($row->reserved_at);
    }

    public function testAReleasedJobWithADelayIsNotImmediatelyAvailable() {
        $queue = $this->makeQueue();
        $queue->push('PekerjaanUji');
        $job = $queue->pop();

        $queue->deleteAndRelease('default', $job, 60);

        $this->assertNull($queue->pop());
    }

    public function testClearRemovesEveryJobOnThatQueueOnly() {
        $queue = $this->makeQueue();
        $queue->push('PekerjaanUji');
        $queue->push('PekerjaanUji');
        $queue->pushOn('lainnya', 'PekerjaanUji');

        $this->assertSame(2, $queue->clear('default'));
        $this->assertSame(0, $queue->size());
        $this->assertSame(1, $queue->size('lainnya'));
    }

    public function testGetQueueFallsBackToTheDefault() {
        $queue = $this->makeQueue();

        $this->assertSame('default', $queue->getQueue(null));
        $this->assertSame('lainnya', $queue->getQueue('lainnya'));
    }

    public function testGetDatabaseHandsBackTheConnection() {
        $this->assertSame($this->connection, $this->makeQueue()->getDatabase());
    }

    /**
     * Kunci primernya diturunkan dari nama tabel, bukan dipatok `id`.
     */
    public function testThePrimaryKeyFollowsTheTableName() {
        $this->assertSame(static::TABLE . '_id', $this->makeQueue()->primaryKey());
    }
}
