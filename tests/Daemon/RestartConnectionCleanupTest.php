<?php

use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . '/Support/DaemonFixture.php';

/**
 * A connection that only records whether it was told to disconnect.
 */
class RestartCleanupFakeConnection {
    /**
     * @var int
     */
    public $disconnectCount = 0;

    /**
     * @return void
     */
    public function disconnect() {
        $this->disconnectCount++;
    }
}

/**
 * A connection whose disconnect() throws, standing in for a socket that is
 * already dead by the time the daemon restarts.
 */
class RestartCleanupThrowingConnection {
    /**
     * @return void
     */
    public function disconnect() {
        throw new RuntimeException('socket error on read socket');
    }
}

/**
 * Restarting a daemon must not hand its open sockets to the next generation.
 *
 * CDaemon_ServiceAbstract::restart() re-executes the daemon, and file
 * descriptors survive exec() unless something closes them first. On wa-go this
 * had one queue runner holding 4360 Redis sockets it had never opened itself —
 * every restart inherited the previous pile, and the count only ever grew.
 */
class RestartConnectionCleanupTest extends TestCase {
    /**
     * @var array
     */
    protected $originalRedisConnections = [];

    /**
     * @var array
     */
    protected $originalDatabaseConnections = [];

    protected function setUp(): void {
        parent::setUp();
        $this->originalRedisConnections = $this->readProperty(CRedis::instance(), 'connections');
        $this->originalDatabaseConnections = $this->readProperty(CDatabase::manager(), 'connections');
    }

    protected function tearDown(): void {
        $this->writeProperty(CRedis::instance(), 'connections', $this->originalRedisConnections);
        $this->writeProperty(CDatabase::manager(), 'connections', $this->originalDatabaseConnections);
        parent::tearDown();
    }

    /**
     * @param object $object
     * @param string $name
     *
     * @return mixed
     */
    protected function readProperty($object, $name) {
        $property = new ReflectionProperty(get_class($object), $name);
        $property->setAccessible(true);

        return $property->getValue($object);
    }

    /**
     * @param object $object
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    protected function writeProperty($object, $name, $value) {
        $property = new ReflectionProperty(get_class($object), $name);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    /**
     * @return void
     */
    protected function invokeCleanup(CDaemon_ServiceAbstract $service) {
        $method = new ReflectionMethod(CDaemon_ServiceAbstract::class, 'closeInheritedConnections');
        $method->setAccessible(true);
        $method->invoke($service);
    }

    /**
     * @return CDaemon_ServiceAbstract
     */
    protected function makeService() {
        $reflection = new ReflectionClass(DaemonTestService::class);

        return $reflection->newInstanceWithoutConstructor();
    }

    public function testPurgeDisconnectsBeforeDroppingTheConnection() {
        $connection = new RestartCleanupFakeConnection();
        $this->writeProperty(CRedis::instance(), 'connections', ['default' => $connection]);

        CRedis::instance()->purge('default');

        $this->assertSame(1, $connection->disconnectCount, 'purge() harus menutup soketnya, bukan sekadar melepas dari cache');
        $this->assertSame([], CRedis::instance()->connections());
    }

    /**
     * Without a name purge() has always meant the default connection.
     */
    public function testPurgeWithoutNameTargetsTheDefaultConnection() {
        $default = new RestartCleanupFakeConnection();
        $cache = new RestartCleanupFakeConnection();
        $this->writeProperty(CRedis::instance(), 'connections', ['default' => $default, 'cache' => $cache]);

        CRedis::instance()->purge();

        $this->assertSame(1, $default->disconnectCount);
        $this->assertSame(0, $cache->disconnectCount);
        $this->assertSame(['cache'], array_keys(CRedis::instance()->connections()));
    }

    /**
     * A connection object that predates disconnect() must still be dropped.
     */
    public function testPurgeStillDropsConnectionThatCannotDisconnect() {
        $connection = new stdClass();
        $this->writeProperty(CRedis::instance(), 'connections', ['default' => $connection]);

        CRedis::instance()->purge('default');

        $this->assertSame([], CRedis::instance()->connections());
    }

    public function testRestartCleanupClosesEveryRedisConnection() {
        $default = new RestartCleanupFakeConnection();
        $cache = new RestartCleanupFakeConnection();
        $queue = new RestartCleanupFakeConnection();
        $this->writeProperty(CRedis::instance(), 'connections', [
            'default' => $default,
            'cache' => $cache,
            'queue' => $queue,
        ]);

        $this->invokeCleanup($this->makeService());

        $this->assertSame(1, $default->disconnectCount);
        $this->assertSame(1, $cache->disconnectCount);
        $this->assertSame(1, $queue->disconnectCount);
        $this->assertSame([], CRedis::instance()->connections(), 'tidak boleh ada sisa koneksi yang ikut terbawa exec()');
    }

    public function testRestartCleanupClosesEveryDatabaseConnection() {
        $primary = new RestartCleanupFakeConnection();
        $report = new RestartCleanupFakeConnection();
        $this->writeProperty(CDatabase::manager(), 'connections', [
            'primary' => $primary,
            'report' => $report,
        ]);

        $this->invokeCleanup($this->makeService());

        $this->assertSame(1, $primary->disconnectCount);
        $this->assertSame(1, $report->disconnectCount);
    }

    /**
     * The daemon must still restart when a connection cannot be closed —
     * losing the restart costs far more than leaking one descriptor.
     */
    public function testRestartCleanupSurvivesAConnectionThatThrows() {
        $healthy = new RestartCleanupFakeConnection();
        $this->writeProperty(CRedis::instance(), 'connections', [
            'default' => new RestartCleanupThrowingConnection(),
            'cache' => $healthy,
        ]);

        $this->invokeCleanup($this->makeService());

        $this->assertSame(1, $healthy->disconnectCount, 'koneksi lain tetap harus ditutup walau satu gagal');
        $this->assertSame([], CRedis::instance()->connections());
    }

    /**
     * A daemon that never touched Redis or the database still restarts.
     */
    public function testRestartCleanupIsHarmlessWhenNothingWasConnected() {
        $this->writeProperty(CRedis::instance(), 'connections', []);
        $this->writeProperty(CDatabase::manager(), 'connections', []);

        $this->invokeCleanup($this->makeService());

        $this->assertSame([], CRedis::instance()->connections());
    }
}
