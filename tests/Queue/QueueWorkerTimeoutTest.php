<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;

class QueueWorkerTimeoutFakeJob extends CQueue_AbstractJob {
    /**
     * @var bool
     */
    public $fired = false;

    /**
     * @var null|Throwable
     */
    public $failedWith;

    /**
     * @var int
     */
    public $attempts = 1;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var null|callable
     */
    protected $callback;

    /**
     * @param string        $id
     * @param array         $payload
     * @param null|callable $callback
     */
    public function __construct($id = 'job-1', array $payload = [], $callback = null) {
        $this->id = $id;
        $this->connectionName = 'test-connection';
        $this->queue = 'default';
        $this->callback = $callback;
        $this->body = json_encode($payload + [
            'uuid' => $id,
            'displayName' => 'QueueWorkerTimeoutFakeJob',
            'job' => 'QueueWorkerTimeoutFakeJob@handle',
            'maxTries' => null,
            'maxExceptions' => null,
            'timeout' => null,
            'failOnTimeout' => false,
            'data' => [],
        ]);
    }

    /**
     * @return string
     */
    public function getJobId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRawBody() {
        return $this->body;
    }

    /**
     * @return int
     */
    public function attempts() {
        return $this->attempts;
    }

    /**
     * @return void
     */
    public function fire() {
        $this->fired = true;
        if ($this->callback != null) {
            call_user_func($this->callback, $this);
        }
    }

    /**
     * @param null|Throwable $e
     *
     * @return void
     */
    public function fail($e = null) {
        $this->failed = true;
        $this->failedWith = $e;
    }
}

class QueueWorkerTimeoutFakeConnection {
    /**
     * @var CQueue_AbstractJob[]
     */
    public $jobList = [];

    /**
     * @param CQueue_AbstractJob[] $jobList
     */
    public function __construct(array $jobList = []) {
        $this->jobList = $jobList;
    }

    /**
     * @param null|string $queue
     *
     * @return null|CQueue_AbstractJob
     */
    public function pop($queue = null) {
        return array_shift($this->jobList);
    }
}

class QueueWorkerTimeoutFakeManager extends CQueue_Manager {
    /**
     * @var QueueWorkerTimeoutFakeConnection
     */
    protected $fakeConnection;

    /**
     * @param QueueWorkerTimeoutFakeConnection $connection
     */
    public function __construct($connection) {
        $this->fakeConnection = $connection;
    }

    /**
     * @param null|string $name
     *
     * @return QueueWorkerTimeoutFakeConnection
     */
    public function connection($name = null) {
        return $this->fakeConnection;
    }
}

/**
 * Records the calls that would otherwise end the process, since both stop() and
 * kill() call exit() and would take PHPUnit down with them.
 */
class QueueWorkerTimeoutTestWorker extends CQueue_Worker {
    /**
     * @var int[]
     */
    public $sleptFor = [];

    /**
     * @var bool
     */
    public $killed = false;

    /**
     * @var null|int
     */
    public $killedWith;

    /**
     * @param int $seconds
     *
     * @return void
     */
    public function sleep($seconds) {
        $this->sleptFor[] = $seconds;
    }

    /**
     * @param int $status
     *
     * @return int
     */
    public function kill($status = 0) {
        $this->killed = true;
        $this->killedWith = $status;

        return $status;
    }
}

/**
 * Timeout enforcement on the `once` code path, backported from CF 1.9.
 *
 * registerTimeoutHandler() used to be called only from daemon(), while
 * CQueue_Runner defaults to `once => true` and therefore runs runNextJob().
 * CQueue::run() consequently had no timeout enforcement of any kind, and a
 * frozen job held its caller forever -- which in CF is a long-lived CDaemon,
 * not a CLI process that would have exited anyway as it does in Laravel.
 *
 * Two production incidents came from exactly that: a daemon stuck on one email
 * job burning a full core for 90 days, and three queue consumers frozen for 12
 * days. Both processes reported Running throughout.
 */
class QueueWorkerTimeoutTest extends TestCase {
    protected function setUp() {
        if (!extension_loaded('pcntl')) {
            $this->markTestSkipped('The pcntl extension is required for timeout enforcement.');
        }
        pcntl_alarm(0);
    }

    protected function tearDown() {
        pcntl_alarm(0);
        m::close();
    }

    /**
     * @param CQueue_AbstractJob[] $jobList
     *
     * @return QueueWorkerTimeoutTestWorker
     */
    protected function makeWorker(array $jobList = []) {
        $handler = m::mock(CException_ExceptionHandler::class);
        $handler->shouldIgnoreMissing();

        return new QueueWorkerTimeoutTestWorker(
            new QueueWorkerTimeoutFakeManager(new QueueWorkerTimeoutFakeConnection($jobList)),
            new CEvent_Dispatcher(),
            $handler,
            function () {
                return false;
            }
        );
    }

    /**
     * @param int $timeout
     * @param int $maxTries
     *
     * @return CQueue_WorkerOptions
     */
    protected function makeOptions($timeout = 60, $maxTries = 5) {
        return new CQueue_WorkerOptions('default', 0, 128, $timeout, 3, $maxTries);
    }

    /**
     * The alarm has to be armed before the job runs, otherwise there is nothing
     * to interrupt a job that never comes back.
     */
    public function testRunNextJobArmsTheAlarmWhileTheJobRuns() {
        $armedFor = null;
        // pcntl_alarm(0) answers with the seconds left on the pending alarm,
        // which is the only way to read it from inside the job.
        $job = new QueueWorkerTimeoutFakeJob('job-1', [], function () use (&$armedFor) {
            $armedFor = pcntl_alarm(0);
        });
        $worker = $this->makeWorker([$job]);

        $worker->runNextJob('test-connection', 'default', $this->makeOptions(30));

        $this->assertTrue($job->fired);
        $this->assertNotNull($armedFor, 'The job ran without any alarm pending.');
        $this->assertGreaterThan(0, $armedFor);
        $this->assertLessThanOrEqual(30, $armedFor);
    }

    /**
     * And it has to be disarmed afterwards. Unlike daemon(), this method returns
     * into a caller that keeps running, so an alarm left pending would fire later
     * during unrelated work and kill that process instead.
     */
    public function testRunNextJobLeavesNoPendingAlarmBehind() {
        $worker = $this->makeWorker([new QueueWorkerTimeoutFakeJob()]);

        $worker->runNextJob('test-connection', 'default', $this->makeOptions(30));

        $this->assertSame(0, pcntl_alarm(0), 'An alarm was still pending after the job finished.');
    }

    public function testRunNextJobLeavesNoPendingAlarmBehindWhenTheQueueIsEmpty() {
        $worker = $this->makeWorker([]);

        $worker->runNextJob('test-connection', 'default', $this->makeOptions(30));

        $this->assertSame(0, pcntl_alarm(0));
    }

    /**
     * The regression proper. Arming the alarm is not enough on its own: without
     * pcntl_async_signals(true) the signal is delivered but never dispatched,
     * because dispatching it would take a pcntl_signal_dispatch() call that a
     * frozen job never reaches. This test fails on the unfixed code.
     */
    public function testRunNextJobKillsAFrozenJob() {
        $job = new QueueWorkerTimeoutFakeJob('frozen-job', [], function () {
            sleep(5);
        });
        $worker = $this->makeWorker([$job]);

        $start = microtime(true);
        $worker->runNextJob('test-connection', 'default', $this->makeOptions(1));
        $elapsed = microtime(true) - $start;

        $this->assertTrue($worker->killed, 'The frozen job was never interrupted.');
        $this->assertSame(CQueue_Worker::EXIT_ERROR, $worker->killedWith);
        $this->assertLessThan(5, $elapsed, 'The job ran to completion instead of being cut short.');
    }

    /**
     * A timeout of 0 means no timeout, and several Tribelio services rely on it
     * -- SqsExport passes `timeout => 0` on purpose. Those callers must keep
     * behaving exactly as they did before this backport.
     */
    public function testATimeoutOfZeroDisablesTheAlarmEntirely() {
        $armedFor = null;
        $job = new QueueWorkerTimeoutFakeJob('job-1', [], function () use (&$armedFor) {
            $armedFor = pcntl_alarm(0);
        });
        $worker = $this->makeWorker([$job]);

        $worker->runNextJob('test-connection', 'default', $this->makeOptions(0));

        $this->assertTrue($job->fired);
        $this->assertSame(0, $armedFor, 'A timeout of 0 must leave no alarm pending.');
        $this->assertFalse($worker->killed);
    }

    /**
     * An explicit timeout on the job beats the worker default, which is how
     * SqsTaskBroadcastNo1's `timeout => 10 * 60` finally takes effect.
     */
    public function testJobTimeoutTakesPrecedenceOverTheWorkerTimeout() {
        $worker = $this->makeWorker();
        $method = new ReflectionMethod(CQueue_Worker::class, 'timeoutForJob');
        $method->setAccessible(true);

        $this->assertSame(
            15,
            $method->invoke($worker, new QueueWorkerTimeoutFakeJob('job-1', ['timeout' => 15]), $this->makeOptions(60))
        );
        $this->assertSame(
            60,
            $method->invoke($worker, new QueueWorkerTimeoutFakeJob(), $this->makeOptions(60))
        );
    }

    /**
     * The default is deliberately loose on this branch: until the fix above,
     * nothing on the `once` path was ever timed out, so a tighter default would
     * start killing jobs that are merely slow rather than frozen.
     */
    public function testTheRunnerDefaultTimeoutIsLooseEnoughNotToKillSlowJobs() {
        $worker = $this->makeWorker();
        $runner = new CQueue_Runner($worker, null, []);

        $method = new ReflectionMethod(CQueue_Runner::class, 'gatherWorkerOptions');
        $method->setAccessible(true);
        $options = $method->invoke($runner);

        $this->assertGreaterThanOrEqual(1800, $options->timeout);
    }

    /**
     * And an explicit timeout still wins over that default.
     */
    public function testAnExplicitTimeoutStillWinsOverTheDefault() {
        $worker = $this->makeWorker();

        $method = new ReflectionMethod(CQueue_Runner::class, 'gatherWorkerOptions');
        $method->setAccessible(true);

        $options = $method->invoke(new CQueue_Runner($worker, null, ['timeout' => 600]));
        $this->assertSame(600, $options->timeout);

        $options = $method->invoke(new CQueue_Runner($worker, null, ['timeout' => 0]));
        $this->assertSame(0, $options->timeout);
    }
}
