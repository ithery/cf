<?php

use PHPUnit\Framework\TestCase;

class BusDispatcherTestCommand {
    /**
     * @var array
     */
    public static $dijalankan = [];

    /**
     * @var string
     */
    public $nilai;

    public function __construct($nilai = 'a') {
        $this->nilai = $nilai;
    }

    /**
     * @return string
     */
    public function handle() {
        static::$dijalankan[] = $this->nilai;

        return 'hasil:' . $this->nilai;
    }
}

class BusDispatcherTestCommandWithExecute {
    /**
     * @return string
     */
    public function execute() {
        return 'lewat-execute';
    }
}

class BusDispatcherTestInvokableCommand {
    /**
     * @return string
     */
    public function __invoke() {
        return 'lewat-invoke';
    }
}

class BusDispatcherTestHandledCommand {
}

class BusDispatcherTestHandler {
    /**
     * @param mixed $command
     *
     * @return string
     */
    public function handle($command) {
        return 'lewat-handler';
    }
}

class BusDispatcherTestQueuedCommand implements CQueue_ShouldQueueInterface {
    /**
     * @var null|string
     */
    public $connection;

    /**
     * @var null|string
     */
    public $queue;

    /**
     * @var null|int
     */
    public $delay;

    /**
     * @return void
     */
    public function handle() {
    }
}

class BusDispatcherTestQueueableCommand implements CQueue_ShouldQueueInterface {
    use CQueue_Trait_QueueableTrait;

    /**
     * @return void
     */
    public function handle() {
    }
}

class BusDispatcherTestSelfQueueingCommand implements CQueue_ShouldQueueInterface {
    /**
     * @var null|string
     */
    public $connection;

    /**
     * @param mixed $queue
     * @param mixed $command
     *
     * @return string
     */
    public function queue($queue, $command) {
        return 'antre-sendiri';
    }
}

class BusDispatcherTestFakeQueue implements CQueue_QueueInterface {
    /**
     * @var array
     */
    public $panggilan = [];

    public function size($queue = null) {
        return 0;
    }

    public function push($job, $data = '', $queue = null) {
        $this->panggilan[] = ['push', $queue, null];
    }

    public function pushRaw($payload, $queue = null, array $options = []) {
        $this->panggilan[] = ['pushRaw', $queue, null];
    }

    public function later($delay, $job, $data = '', $queue = null) {
        $this->panggilan[] = ['later', $queue, $delay];
    }

    public function pushOn($queue, $job, $data = '') {
        $this->panggilan[] = ['pushOn', $queue, null];
    }

    public function laterOn($queue, $delay, $job, $data = '') {
        $this->panggilan[] = ['laterOn', $queue, $delay];
    }

    public function bulk($jobs, $data = '', $queue = null) {
        $this->panggilan[] = ['bulk', $queue, null];
    }

    public function pop($queue = null) {
        return null;
    }

    public function getConnectionName() {
        return 'palsu';
    }

    public function setConnectionName($name) {
        return $this;
    }
}

class BusDispatcherTestPipe {
    /**
     * @var array
     */
    public static $dilewati = [];

    /**
     * @param mixed    $command
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($command, $next) {
        static::$dilewati[] = get_class($command);

        return $next($command);
    }
}

class BusDispatcherTest extends TestCase {
    protected function setUp() {
        BusDispatcherTestCommand::$dijalankan = [];
        BusDispatcherTestPipe::$dilewati = [];
        CQueue::routes()->flush();
    }

    protected function tearDown() {
        CQueue::routes()->flush();
    }

    /**
     * @param null|\Closure $queueResolver
     *
     * @return CQueue_Dispatcher
     */
    protected function makeDispatcher($queueResolver = null) {
        return new CQueue_Dispatcher(CContainer::getInstance(), $queueResolver);
    }

    public function testACommandIsHandledByItsOwnHandleMethod() {
        $hasil = $this->makeDispatcher()->dispatch(new BusDispatcherTestCommand('a'));

        $this->assertSame('hasil:a', $hasil);
        $this->assertSame(['a'], BusDispatcherTestCommand::$dijalankan);
    }

    /**
     * execute() didahulukan atas handle(), dan __invoke() dipakai kalau tidak
     * ada keduanya -- perintah lama di CF memakai execute().
     */
    public function testExecuteAndInvokeAreAcceptedToo() {
        $dispatcher = $this->makeDispatcher();

        $this->assertSame('lewat-execute', $dispatcher->dispatch(new BusDispatcherTestCommandWithExecute()));
        $this->assertSame('lewat-invoke', $dispatcher->dispatch(new BusDispatcherTestInvokableCommand()));
    }

    public function testAMappedHandlerTakesOverFromTheCommand() {
        $dispatcher = $this->makeDispatcher();
        $dispatcher->map([BusDispatcherTestHandledCommand::class => BusDispatcherTestHandler::class]);

        $this->assertTrue($dispatcher->hasCommandHandler(new BusDispatcherTestHandledCommand()));
        $this->assertSame('lewat-handler', $dispatcher->dispatch(new BusDispatcherTestHandledCommand()));
    }

    public function testThereIsNoHandlerUntilOneIsMapped() {
        $dispatcher = $this->makeDispatcher();

        $this->assertFalse($dispatcher->hasCommandHandler(new BusDispatcherTestHandledCommand()));
        $this->assertFalse($dispatcher->getCommandHandler(new BusDispatcherTestHandledCommand()));
    }

    public function testMapReturnsTheDispatcherForChaining() {
        $dispatcher = $this->makeDispatcher();

        $this->assertSame($dispatcher, $dispatcher->map([]));
        $this->assertSame($dispatcher, $dispatcher->pipeThrough([]));
    }

    /**
     * Pipa berjalan mengelilingi tiap perintah, itulah tempat middleware bus
     * dipasang.
     */
    public function testEveryCommandPassesThroughThePipes() {
        $dispatcher = $this->makeDispatcher()->pipeThrough([BusDispatcherTestPipe::class]);
        $hasil = $dispatcher->dispatch(new BusDispatcherTestCommand('a'));

        $this->assertSame('hasil:a', $hasil);
        $this->assertSame([BusDispatcherTestCommand::class], BusDispatcherTestPipe::$dilewati);
    }

    /**
     * Tanpa penyelesai antrean, perintah beriantrean pun dijalankan langsung --
     * itu yang membuat kode yang sama bekerja di lingkungan tanpa antrean.
     */
    public function testAQueueableCommandRunsInlineWhenThereIsNoQueueResolver() {
        $hasil = $this->makeDispatcher()->dispatch(new BusDispatcherTestQueuedCommand());

        $this->assertNull($hasil);
    }

    public function testAQueueableCommandGoesToTheQueue() {
        $queue = new BusDispatcherTestFakeQueue();
        $dispatcher = $this->makeDispatcher(function () use ($queue) {
            return $queue;
        });

        $dispatcher->dispatch(new BusDispatcherTestQueuedCommand());

        $this->assertSame([['push', null, null]], $queue->panggilan);
    }

    public function testAPlainCommandStillRunsInlineEvenWithAQueueResolver() {
        $queue = new BusDispatcherTestFakeQueue();
        $dispatcher = $this->makeDispatcher(function () use ($queue) {
            return $queue;
        });

        $this->assertSame('hasil:a', $dispatcher->dispatch(new BusDispatcherTestCommand('a')));
        $this->assertSame([], $queue->panggilan);
    }

    public function testTheQueueNameOnTheCommandIsHonoured() {
        $queue = new BusDispatcherTestFakeQueue();
        $dispatcher = $this->makeDispatcher(function () use ($queue) {
            return $queue;
        });

        $command = new BusDispatcherTestQueuedCommand();
        $command->queue = 'khusus';
        $dispatcher->dispatch($command);

        $this->assertSame([['pushOn', 'khusus', null]], $queue->panggilan);
    }

    public function testADelayOnTheCommandBecomesALaterPush() {
        $queue = new BusDispatcherTestFakeQueue();
        $dispatcher = $this->makeDispatcher(function () use ($queue) {
            return $queue;
        });

        $command = new BusDispatcherTestQueuedCommand();
        $command->delay = 60;
        $dispatcher->dispatch($command);

        $this->assertSame([['later', null, 60]], $queue->panggilan);
    }

    public function testAQueueNameAndADelayTogetherBecomeLaterOn() {
        $queue = new BusDispatcherTestFakeQueue();
        $dispatcher = $this->makeDispatcher(function () use ($queue) {
            return $queue;
        });

        $command = new BusDispatcherTestQueuedCommand();
        $command->queue = 'khusus';
        $command->delay = 60;
        $dispatcher->dispatch($command);

        $this->assertSame([['laterOn', 'khusus', 60]], $queue->panggilan);
    }

    /**
     * Sambungan yang diminta perintah diteruskan ke penyelesai, karena dari
     * situlah antrean yang benar dipilih.
     */
    public function testTheConnectionOnTheCommandReachesTheResolver() {
        $diminta = null;
        $dispatcher = $this->makeDispatcher(function ($connection) use (&$diminta) {
            $diminta = $connection;

            return new BusDispatcherTestFakeQueue();
        });

        $command = new BusDispatcherTestQueuedCommand();
        $command->connection = 'redis';
        $dispatcher->dispatch($command);

        $this->assertSame('redis', $diminta);
    }

    /**
     * Rute mengisi yang tidak ditulis job sendiri; yang ditulis job tetap menang.
     */
    public function testARouteSuppliesTheConnectionAndQueueWhenTheCommandDoesNot() {
        CQueue::routes()->set(BusDispatcherTestQueuedCommand::class, 'lambat', 'redis');

        $diminta = null;
        $queue = new BusDispatcherTestFakeQueue();
        $dispatcher = $this->makeDispatcher(function ($connection) use (&$diminta, $queue) {
            $diminta = $connection;

            return $queue;
        });

        $dispatcher->dispatch(new BusDispatcherTestQueuedCommand());

        $this->assertSame('redis', $diminta);
        $this->assertSame([['pushOn', 'lambat', null]], $queue->panggilan);
    }

    public function testWhatTheCommandStatesBeatsTheRoute() {
        CQueue::routes()->set(BusDispatcherTestQueuedCommand::class, 'lambat', 'redis');

        $diminta = null;
        $queue = new BusDispatcherTestFakeQueue();
        $dispatcher = $this->makeDispatcher(function ($connection) use (&$diminta, $queue) {
            $diminta = $connection;

            return $queue;
        });

        $command = new BusDispatcherTestQueuedCommand();
        $command->connection = 'sqs';
        $command->queue = 'cepat';
        $dispatcher->dispatch($command);

        $this->assertSame('sqs', $diminta);
        $this->assertSame([['pushOn', 'cepat', null]], $queue->panggilan);
    }

    /**
     * Perintah yang tahu cara mengantrekan dirinya sendiri mengambil alih;
     * itulah jalur yang dipakai rantai dan gugus job.
     */
    public function testACommandThatQueuesItselfTakesOver() {
        $queue = new BusDispatcherTestFakeQueue();
        $dispatcher = $this->makeDispatcher(function () use ($queue) {
            return $queue;
        });

        $hasil = $dispatcher->dispatch(new BusDispatcherTestSelfQueueingCommand());

        $this->assertSame('antre-sendiri', $hasil);
        $this->assertSame([], $queue->panggilan);
    }

    public function testAResolverThatDoesNotReturnAQueueIsRefused() {
        $dispatcher = $this->makeDispatcher(function () {
            return 'bukan antrean';
        });

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Queue resolver did not return a Queue implementation.');

        $dispatcher->dispatch(new BusDispatcherTestQueuedCommand());
    }

    /**
     * dispatchSync memaksa sambungan `sync`, jadi perintah beriantrean pun
     * selesai sebelum panggilan itu kembali.
     */
    public function testDispatchSyncSendsAQueueableCommandToTheSyncConnection() {
        $diminta = null;
        $dispatcher = $this->makeDispatcher(function ($connection) use (&$diminta) {
            $diminta = $connection;

            return new BusDispatcherTestFakeQueue();
        });

        $dispatcher->dispatchSync(new BusDispatcherTestQueueableCommand());

        $this->assertSame('sync', $diminta);
    }

    /**
     * Perintah yang tidak punya onConnection() tidak dapat dipaksa ke sambungan
     * sync, jadi ia dijalankan langsung -- bukan diam-diam masuk antrean lain.
     */
    public function testDispatchSyncRunsInlineWhenTheCommandCannotBeRedirected() {
        $diminta = null;
        $dispatcher = $this->makeDispatcher(function ($connection) use (&$diminta) {
            $diminta = $connection;

            return new BusDispatcherTestFakeQueue();
        });

        $dispatcher->dispatchSync(new BusDispatcherTestQueuedCommand());

        $this->assertNull($diminta);
    }

    /**
     * onConnection() dan onQueue() menulis pilihannya pada perintah, dan itulah
     * yang kemudian dibaca saat penyaluran -- keduanya terpisah: sambungan
     * menentukan pialangnya, antrean menentukan larasnya.
     */
    public function testOnConnectionAndOnQueueAreBothHonouredWhenDispatching() {
        $diminta = null;
        $queue = new BusDispatcherTestFakeQueue();
        $dispatcher = $this->makeDispatcher(function ($connection) use (&$diminta, $queue) {
            $diminta = $connection;

            return $queue;
        });

        $command = new BusDispatcherTestQueueableCommand();
        $command->onConnection('redis')->onQueue('lambat');
        $dispatcher->dispatch($command);

        $this->assertSame('redis', $diminta);
        $this->assertSame([['pushOn', 'lambat', null]], $queue->panggilan);
    }

    public function testADelaySetThroughTheTraitBecomesALaterPush() {
        $queue = new BusDispatcherTestFakeQueue();
        $dispatcher = $this->makeDispatcher(function () use ($queue) {
            return $queue;
        });

        $command = new BusDispatcherTestQueueableCommand();
        $command->delay(90);
        $dispatcher->dispatch($command);

        $this->assertSame([['later', null, 90]], $queue->panggilan);
    }

    public function testDispatchNowAlwaysRunsInline() {
        $queue = new BusDispatcherTestFakeQueue();
        $dispatcher = $this->makeDispatcher(function () use ($queue) {
            return $queue;
        });

        $dispatcher->dispatchNow(new BusDispatcherTestCommand('a'));

        $this->assertSame(['a'], BusDispatcherTestCommand::$dijalankan);
        $this->assertSame([], $queue->panggilan);
    }

    public function testDispatchNowAcceptsAnExplicitHandler() {
        $hasil = $this->makeDispatcher()->dispatchNow(
            new BusDispatcherTestHandledCommand(),
            new BusDispatcherTestHandler()
        );

        $this->assertSame('lewat-handler', $hasil);
    }

    /**
     * Tanpa izin menunda ke sesudah respons, perintahnya dijalankan saat itu
     * juga alih-alih hilang.
     */
    public function testDispatchAfterResponseRunsInlineWhenDeferringIsOff() {
        $dispatcher = $this->makeDispatcher()->withoutDispatchingAfterResponses();
        $dispatcher->dispatchAfterResponse(new BusDispatcherTestCommand('a'));

        $this->assertSame(['a'], BusDispatcherTestCommand::$dijalankan);
    }

    public function testTheDeferringSwitchesReturnTheDispatcher() {
        $dispatcher = $this->makeDispatcher();

        $this->assertSame($dispatcher, $dispatcher->withDispatchingAfterResponses());
        $this->assertSame($dispatcher, $dispatcher->withoutDispatchingAfterResponses());
    }
}
