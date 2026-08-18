<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . '/Support/WorkerFixture.php';

/**
 * Timeout enforcement on the `once` code path.
 *
 * `registerTimeoutHandler()` used to be called only from `daemon()`, while
 * `CQueue_Runner` defaults to `once => true` and therefore runs `runNextJob()`.
 * `CQueue::run()` consequently had no timeout enforcement of any kind, and a
 * frozen job held its caller forever -- which in CF is a long-lived CDaemon,
 * not a CLI process that would have exited anyway as it does in Laravel.
 *
 * Two production incidents came from exactly that: a daemon stuck on one email
 * job burning a full core for 90 days, and three queue consumers frozen for 12
 * days. Both processes reported `Running` throughout.
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
     * @return QueueWorkerTestWorker
     */
    protected function makeWorker(array $jobList = []) {
        $handler = m::mock(CException_ExceptionHandler::class);
        $handler->shouldIgnoreMissing();

        return new QueueWorkerTestWorker(
            new QueueWorkerFakeManager(new QueueWorkerFakeConnection($jobList)),
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
     * The alarm has to be armed *before* the job runs, otherwise there is
     * nothing to interrupt a job that never comes back.
     */
    public function testRunNextJobArmsTheAlarmWhileTheJobRuns() {
        $armedFor = null;
        // pcntl_alarm(0) answers with the seconds left on the pending alarm,
        // which is the only way to read it from inside the job.
        $job = new QueueWorkerFakeJob('job-1', [], function () use (&$armedFor) {
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
     * And it has to be disarmed afterwards. Unlike `daemon()`, this method
     * returns into a caller that keeps running, so an alarm left pending would
     * fire later during unrelated work and kill that process instead.
     */
    public function testRunNextJobLeavesNoPendingAlarmBehind() {
        $worker = $this->makeWorker([new QueueWorkerFakeJob()]);

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
     * `pcntl_async_signals(true)` the signal is delivered but never dispatched,
     * because dispatching it would take a `pcntl_signal_dispatch()` call that a
     * frozen job never reaches. This test fails on the unfixed code.
     */
    public function testRunNextJobKillsAFrozenJob() {
        $job = new QueueWorkerFakeJob('frozen-job', [], function () {
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
     * A job under its retry limit must not be marked failed just because it was
     * cut short -- the timeout kills the process and the job goes back on the
     * queue for the next worker.
     */
    public function testAFrozenJobUnderItsRetryLimitIsNotMarkedFailed() {
        $job = new QueueWorkerFakeJob('frozen-job', [], function () {
            sleep(5);
        });
        $job->attempts = 1;
        $worker = $this->makeWorker([$job]);

        $worker->runNextJob('test-connection', 'default', $this->makeOptions(1, 5));

        $this->assertTrue($worker->killed);
        $this->assertFalse($job->hasFailed());
    }

    /**
     * On its last attempt it must be, otherwise a job that times out every time
     * is retried forever and never lands in the failed table.
     */
    public function testAFrozenJobOnItsLastAttemptIsMarkedFailed() {
        $job = new QueueWorkerFakeJob('frozen-job', [], function () {
            sleep(5);
        });
        $job->attempts = 3;
        $worker = $this->makeWorker([$job]);

        $worker->runNextJob('test-connection', 'default', $this->makeOptions(1, 3));

        $this->assertTrue($worker->killed);
        $this->assertTrue($job->hasFailed());
        // A timeout is reported as a timeout, not as "attempted too many times".
        $this->assertInstanceOf(CQueue_Exception_TimeoutExceededException::class, $job->failedWith);
        $this->assertSame($job, $job->failedWith->job);
    }

    /**
     * The kill has to carry why it happened, so a WorkerStopping listener can
     * tell a timeout apart from a memory limit or a restart signal.
     */
    public function testTheKillCarriesTheTimedOutReason() {
        $job = new QueueWorkerFakeJob('frozen-job', [], function () {
            sleep(5);
        });
        $worker = $this->makeWorker([$job]);

        $worker->runNextJob('test-connection', 'default', $this->makeOptions(1));

        $this->assertSame(CQueue_WorkerStopReason::TIMED_OUT, $worker->killedReason);
    }

    public function testJobTimedOutEventIsDispatched() {
        $dispatched = [];
        $dispatcher = new CEvent_Dispatcher();
        $dispatcher->listen(CQueue_Event_JobTimedOut::class, function ($e) use (&$dispatched) {
            $dispatched[] = $e;
        });

        $handler = m::mock(CException_ExceptionHandler::class);
        $handler->shouldIgnoreMissing();
        $job = new QueueWorkerFakeJob('frozen-job', [], function () {
            sleep(5);
        });
        $worker = new QueueWorkerTestWorker(
            new QueueWorkerFakeManager(new QueueWorkerFakeConnection([$job])),
            $dispatcher,
            $handler,
            function () {
                return false;
            }
        );

        $worker->runNextJob('test-connection', 'default', $this->makeOptions(1));

        $this->assertCount(1, $dispatched);
        $this->assertSame($job, $dispatched[0]->job);
    }

    /**
     * The daemon path already worked; keep it that way.
     */
    public function testDaemonAlsoArmsTheAlarmWhileTheJobRuns() {
        $armedFor = null;
        $job = new QueueWorkerFakeJob('job-1', [], function () use (&$armedFor) {
            $armedFor = pcntl_alarm(0);
        });
        $worker = $this->makeWorker([$job]);
        // stopWhenEmpty ends the loop on the iteration after the job.
        $options = new CQueue_WorkerOptions('default', 0, 128, 30, 0, 5, false, true);

        $worker->daemon('test-connection', 'default', $options);

        $this->assertTrue($job->fired);
        $this->assertTrue($worker->stopped);
        $this->assertNotNull($armedFor, 'The job ran without any alarm pending.');
        $this->assertGreaterThan(0, $armedFor);
    }

    /**
     * A timeout of 0 means no timeout, and callers rely on it -- Tribelio's
     * SqsExport passes `timeout => 0` on purpose. Those callers must keep
     * behaving exactly as they did before timeout enforcement existed.
     */
    public function testATimeoutOfZeroDisablesTheAlarmEntirely() {
        $armedFor = null;
        $job = new QueueWorkerFakeJob('job-1', [], function () use (&$armedFor) {
            $armedFor = pcntl_alarm(0);
        });
        $worker = $this->makeWorker([$job]);

        $worker->runNextJob('test-connection', 'default', $this->makeOptions(0));

        $this->assertTrue($job->fired);
        $this->assertSame(0, $armedFor, 'A timeout of 0 must leave no alarm pending.');
        $this->assertFalse($worker->killed);
    }

    /**
     * Bawaannya 300 detik di cabang ini. Cabang CF1.6 sengaja memakai 1800 --
     * di sana jalur `once` belum pernah menegakkan timeout sama sekali sebelum
     * backport, sehingga bawaan yang ketat akan mulai membunuh job yang sekadar
     * lambat. Dikunci di sini supaya penyusulan CF1.6 ke depan tidak diam-diam
     * ikut membawa angkanya.
     */
    public function testTheRunnerDefaultTimeoutStaysAtFiveMinutes() {
        $worker = $this->makeWorker();
        $method = new ReflectionMethod(CQueue_Runner::class, 'gatherWorkerOptions');
        $method->setAccessible(true);

        $options = $method->invoke(new CQueue_Runner($worker, null, []));

        $this->assertSame(300, $options->timeout);
    }

    public function testAnExplicitTimeoutStillWinsOverTheDefault() {
        $worker = $this->makeWorker();
        $method = new ReflectionMethod(CQueue_Runner::class, 'gatherWorkerOptions');
        $method->setAccessible(true);

        $this->assertSame(600, $method->invoke(new CQueue_Runner($worker, null, ['timeout' => 600]))->timeout);
        $this->assertSame(0, $method->invoke(new CQueue_Runner($worker, null, ['timeout' => 0]))->timeout);
    }

    public function testJobTimeoutTakesPrecedenceOverTheWorkerTimeout() {
        $worker = $this->makeWorker();
        $method = new ReflectionMethod(CQueue_Worker::class, 'timeoutForJob');
        $method->setAccessible(true);

        $this->assertSame(
            15,
            $method->invoke($worker, new QueueWorkerFakeJob('job-1', ['timeout' => 15]), $this->makeOptions(60))
        );
        $this->assertSame(
            60,
            $method->invoke($worker, new QueueWorkerFakeJob(), $this->makeOptions(60))
        );
        $this->assertSame(
            60,
            $method->invoke($worker, null, $this->makeOptions(60))
        );
    }
}
