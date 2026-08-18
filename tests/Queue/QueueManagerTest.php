<?php

use PHPUnit\Framework\TestCase;

class QueueManagerTest extends TestCase {
    /**
     * @return CQueue_Manager
     */
    protected function makeManager() {
        $manager = new CQueue_Manager(new CEvent_Dispatcher());
        CQueue::registerConnectors($manager);

        return $manager;
    }

    /**
     * Manager antrean membaca konfigurasinya langsung dari `queue.connections.*`,
     * tidak lewat pendaftaran seperti manager basis data.
     *
     * @param string $name
     * @param array  $config
     *
     * @return void
     */
    protected function defineConnection($name, array $config) {
        CConfig::instance('queue')->set('connections.' . $name, $config);
    }

    public function testTheDefaultDriverComesFromConfig() {
        $manager = $this->makeManager();

        $this->assertSame(CF::config('queue.default'), $manager->getDefaultDriver());
    }

    public function testTheDefaultDriverCanBeChanged() {
        $manager = $this->makeManager();
        $manager->setDefaultDriver('sync');

        $this->assertSame('sync', $manager->getDefaultDriver());
    }

    public function testGetNameFallsBackToTheDefaultDriver() {
        $manager = $this->makeManager();
        $manager->setDefaultDriver('sync');

        $this->assertSame('sync', $manager->getName());
        $this->assertSame('lainnya', $manager->getName('lainnya'));
    }

    public function testAConnectionIsResolvedFromItsConfig() {
        $manager = $this->makeManager();
        $this->defineConnection('sinkron', ['driver' => 'sync']);

        $this->assertInstanceOf(CQueue_Queue_SyncQueue::class, $manager->connection('sinkron'));
    }

    public function testTheSameConnectionInstanceIsReused() {
        $manager = $this->makeManager();
        $this->defineConnection('sinkron', ['driver' => 'sync']);

        $this->assertSame($manager->connection('sinkron'), $manager->connection('sinkron'));
    }

    public function testConnectedReportsWhetherAConnectionWasBuilt() {
        $manager = $this->makeManager();
        $this->defineConnection('sinkron', ['driver' => 'sync']);

        $this->assertFalse($manager->connected('sinkron'));
        $manager->connection('sinkron');
        $this->assertTrue($manager->connected('sinkron'));
    }

    public function testTheResolvedConnectionCarriesItsName() {
        $manager = $this->makeManager();
        $this->defineConnection('sinkron', ['driver' => 'sync']);

        $this->assertSame('sinkron', $manager->connection('sinkron')->getConnectionName());
    }

    public function testAnUnknownConnectionThrows() {
        $manager = $this->makeManager();

        $this->expectException(InvalidArgumentException::class);
        $manager->connection('tidak-pernah-didaftarkan');
    }

    public function testAnUnknownDriverThrows() {
        $manager = $this->makeManager();
        $this->defineConnection('aneh', ['driver' => 'entah-apa']);

        $this->expectException(InvalidArgumentException::class);
        $manager->connection('aneh');
    }

    /**
     * extend() mendaftarkan sebuah **connector**, bukan antreannya langsung --
     * manager memanggil connect($config) atas hasilnya.
     */
    public function testExtendRegistersACustomDriver() {
        $manager = $this->makeManager();
        $manager->extend('buatan', function () {
            return new QueueManagerTestConnector();
        });
        $this->defineConnection('pakai-buatan', ['driver' => 'buatan']);

        $this->assertInstanceOf(CQueue_Queue_NullQueue::class, $manager->connection('pakai-buatan'));
    }

    public function testTheNullDriverResolves() {
        $manager = $this->makeManager();
        $this->defineConnection('kosong', ['driver' => 'null']);

        $this->assertInstanceOf(CQueue_Queue_NullQueue::class, $manager->connection('kosong'));
    }

    /**
     * The manager forwards anything it does not implement to the default
     * connection, which is what makes `CQueue::queuer()->push(...)` work.
     */
    public function testUnknownCallsAreForwardedToTheDefaultConnection() {
        $manager = $this->makeManager();
        $this->defineConnection('kosong', ['driver' => 'null']);
        $manager->setDefaultDriver('kosong');

        $this->assertSame(0, $manager->size());
    }

    public function testTheMonitorHooksRegisterListeners() {
        $dispatcher = new CEvent_Dispatcher();
        $manager = new CQueue_Manager($dispatcher);
        $seen = [];
        $manager->starting(function () use (&$seen) {
            $seen[] = 'starting';
        });
        $manager->looping(function () use (&$seen) {
            $seen[] = 'looping';
        });
        $manager->failing(function () use (&$seen) {
            $seen[] = 'failing';
        });
        $manager->stopping(function () use (&$seen) {
            $seen[] = 'stopping';
        });
        $manager->before(function () use (&$seen) {
            $seen[] = 'before';
        });
        $manager->after(function () use (&$seen) {
            $seen[] = 'after';
        });
        $manager->exceptionOccurred(function () use (&$seen) {
            $seen[] = 'exception';
        });

        $dispatcher->dispatch(new CQueue_Event_WorkerStarting('sync', 'default'));
        $dispatcher->dispatch(new CQueue_Event_Looping('sync', 'default'));
        $dispatcher->dispatch(new CQueue_Event_WorkerStopping(0));

        $this->assertSame(['starting', 'looping', 'stopping'], $seen);
    }
}

class QueueManagerTestConnector implements CQueue_ConnectorInterface {
    /**
     * @param array $config
     *
     * @return CQueue_QueueInterface
     */
    public function connect(array $config) {
        return new CQueue_Queue_NullQueue();
    }
}
