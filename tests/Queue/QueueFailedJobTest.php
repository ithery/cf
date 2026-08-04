<?php

use PHPUnit\Framework\TestCase;

class QueueFailedJobTest extends TestCase {
    /**
     * @var string
     */
    const TABLE = 'queue_failed';

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
            $table->string('connection');
            $table->string('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->dateTime('failed_at');
        });
    }

    /**
     * @return CQueue_FailedJob_DatabaseFailedJob
     */
    protected function makeProvider() {
        return new CQueue_FailedJob_DatabaseFailedJob($this->connection, static::TABLE);
    }

    /**
     * @param string $job
     *
     * @return string
     */
    protected function payload($job = 'PekerjaanUji') {
        return json_encode(['uuid' => 'uuid-' . $job, 'job' => $job, 'data' => []]);
    }

    public function testLogStoresTheFailureAndGivesBackItsId() {
        $provider = $this->makeProvider();
        $id = $provider->log('database', 'default', $this->payload(), new RuntimeException('meledak'));

        $this->assertNotNull($id);

        $row = $this->connection->table(static::TABLE)->first();

        $this->assertSame('database', $row->connection);
        $this->assertSame('default', $row->queue);
        $this->assertSame($this->payload(), $row->payload);
        $this->assertStringContainsString('meledak', $row->exception);
        $this->assertNotNull($row->failed_at);
    }

    /**
     * Jejak tumpukan ikut disimpan, bukan cuma pesannya -- tanpa itu sebuah
     * kegagalan di produksi tidak dapat ditelusuri sesudah kejadiannya lewat.
     */
    public function testTheStoredExceptionCarriesItsStackTrace() {
        $provider = $this->makeProvider();
        $provider->log('database', 'default', $this->payload(), new RuntimeException('meledak'));

        $this->assertStringContainsString('#0', $this->connection->table(static::TABLE)->first()->exception);
    }

    public function testAllIsEmptyToBeginWith() {
        $this->assertSame([], $this->makeProvider()->all());
    }

    /**
     * Yang terbaru di depan: daftar kegagalan dibaca orang, dan yang baru saja
     * terjadi itulah yang dicari lebih dulu.
     */
    public function testAllReturnsTheNewestFirst() {
        $provider = $this->makeProvider();
        $provider->log('database', 'default', $this->payload('SatuJob'), new RuntimeException('a'));
        $provider->log('database', 'default', $this->payload('DuaJob'), new RuntimeException('b'));

        $semua = $provider->all();

        $this->assertCount(2, $semua);
        $this->assertSame($this->payload('DuaJob'), $semua[0]->payload);
        $this->assertSame($this->payload('SatuJob'), $semua[1]->payload);
    }

    public function testFindReturnsTheOneThatWasAskedFor() {
        $provider = $this->makeProvider();
        $id = $provider->log('database', 'default', $this->payload(), new RuntimeException('meledak'));

        $this->assertSame($this->payload(), $provider->find($id)->payload);
    }

    public function testFindGivesNothingForAnUnknownId() {
        $this->assertNull($this->makeProvider()->find(404));
    }

    public function testFlushEmptiesTheTable() {
        $provider = $this->makeProvider();
        $provider->log('database', 'default', $this->payload(), new RuntimeException('a'));
        $provider->log('database', 'default', $this->payload(), new RuntimeException('b'));

        $provider->flush();

        $this->assertSame([], $provider->all());
    }

    /**
     * forget() menyaring kolom `id`, sedangkan kunci primer tabel ini
     * `queue_failed_id` -- sebagaimana dipakai all() dan find(). Jadi tidak ada
     * baris yang cocok dan penghapusannya selalu gagal tanpa suara: membuang
     * satu job yang gagal tidak pernah berhasil, dan yang tersisa cuma flush()
     * yang membuang semuanya sekaligus.
     *
     * Dicatat apa adanya, belum diperbaiki, karena menunggu keputusan.
     */
    public function testForgetNeverRemovesAnything() {
        $provider = $this->makeProvider();
        $id = $provider->log('database', 'default', $this->payload(), new RuntimeException('meledak'));

        $this->assertFalse($provider->forget($id));
        $this->assertCount(1, $provider->all());
    }

    /**
     * Penyedia null menjawab semua panggilan tanpa menyimpan apa pun; ia dipakai
     * ketika pencatatan kegagalan sengaja dimatikan, sehingga tidak boleh ada
     * satu pun yang melempar.
     */
    public function testTheNullProviderSwallowsEverything() {
        $provider = new CQueue_FailedJob_NullFailedJob();

        $this->assertNull($provider->log('database', 'default', $this->payload(), new RuntimeException('a')));
        $this->assertSame([], $provider->all());
        $this->assertNull($provider->find(1));
        $this->assertTrue($provider->forget(1));
        $this->assertNull($provider->flush());
    }
}
