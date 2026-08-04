<?php

use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . '/Support/DaemonFixture.php';

/**
 * CDaemon has no Laravel counterpart, so these tests are written against CF's
 * own behaviour rather than ported.
 *
 * The focus is the machinery the two 2026 Tribelio incidents ran into: the loop
 * statistics (the only thing a daemon measures about itself today), the
 * auto-restart interval that was supposed to catch a stuck process, and the pid
 * bookkeeping that decides whether a daemon reports Running or Stopped.
 */
class DaemonServiceTest extends TestCase {
    /**
     * @var string
     */
    protected $pidFile;

    protected function setUp() {
        $this->pidFile = sys_get_temp_dir() . '/cf-daemon-test-' . getmypid() . '.pid';
    }

    protected function tearDown() {
        if (file_exists($this->pidFile)) {
            unlink($this->pidFile);
        }
        // CDaemon_ErrorHandler::init() runs from the constructor and takes over
        // the global handlers; leaving them in place would break later tests.
        restore_error_handler();
        restore_exception_handler();
    }

    /**
     * @return DaemonTestService
     */
    protected function makeService(array $config = []) {
        return new DaemonTestService('DaemonTestService', $config + ['pidFile' => $this->pidFile]);
    }

    public function testConfigIsReadable() {
        $service = $this->makeService(['loopInterval' => 5]);

        $this->assertSame(5, $service->getConfig('loopInterval'));
        $this->assertSame($this->pidFile, $service->getConfig('pidFile'));
        $this->assertNull($service->getConfig('nothingHere'));
        $this->assertSame('DaemonTestService', $service->getServiceName());
    }

    /**
     * statsMean() drops the longest and shortest 5% before averaging, so a
     * single outlier iteration must not move the reported mean.
     */
    public function testStatsMeanIgnoresTheExtremes() {
        $service = $this->makeService();
        $statList = [];
        for ($i = 0; $i < 100; $i++) {
            $statList[] = ['duration' => 1.0, 'idle' => 2.0];
        }
        // one iteration that took far longer than the rest
        $statList[50] = ['duration' => 900.0, 'idle' => -898.0];
        $service->setStats($statList);

        list($duration, $idle) = $service->statsMean();

        $this->assertEqualsWithDelta(1.0, $duration, 0.0001, 'A single outlier moved the mean.');
        $this->assertEqualsWithDelta(2.0, $idle, 0.0001);
    }

    public function testStatsMeanOnAnEmptySampleIsZero() {
        $service = $this->makeService();
        $service->setStats([]);

        $this->assertSame([0, 0], $service->statsMean());
    }

    /**
     * The sample is trimmed to the most recent 100 entries, which is what keeps
     * a long-lived daemon from growing this array without bound.
     */
    public function testStatsTrimKeepsTheLastHundred() {
        $service = $this->makeService();
        $statList = [];
        for ($i = 0; $i < 250; $i++) {
            $statList[] = ['duration' => $i, 'idle' => 0];
        }
        $service->setStats($statList);

        $service->statsTrim();

        $trimmed = $service->callProtected('statsMean', [100]);
        // the last 100 entries are durations 150..249, whose trimmed mean is ~199.5
        $this->assertEqualsWithDelta(199.5, $trimmed[0], 1.0);
    }

    public function testLoopIntervalRejectsANonNumericValue() {
        $service = $this->makeService();

        $this->expectException('Exception');
        $service->callProtected('setLoopInterval', ['not a number']);
    }

    public function testLoopIntervalAcceptsSubSecondValues() {
        $service = $this->makeService();

        $service->callProtected('setLoopInterval', [0.5]);

        $this->assertSame(0.5, $service->getLoopInterval());
    }

    /**
     * The floor exists so a misconfigured daemon cannot restart itself in a
     * tight loop. checkEnvironment() reports by throwing, so the message is the
     * only thing to assert on.
     */
    public function testCheckEnvironmentRejectsAnAutoRestartIntervalBelowTheFloor() {
        $service = $this->makeService();
        $service->setProperty('loopInterval', 1);
        $service->setProperty('autoRestartInterval', CDaemon_ServiceAbstract::MIN_RESTART_SECONDS - 1);

        try {
            $service->callProtected('checkEnvironment', [[]]);
            $this->fail('An auto-restart interval below the floor was accepted.');
        } catch (Exception $ex) {
            $this->assertTrue(strpos($ex->getMessage(), 'Auto-restart') !== false, $ex->getMessage());
        }
    }

    public function testCheckEnvironmentAcceptsSaneIntervals() {
        $service = $this->makeService();
        $service->setProperty('loopInterval', 1);
        $service->setProperty('autoRestartInterval', 43200);

        $service->callProtected('checkEnvironment', [[]]);

        // no exception means the configuration passed
        $this->assertTrue(true);
    }

    public function testCheckEnvironmentRejectsANonNumericLoopInterval() {
        $service = $this->makeService();
        $service->setProperty('loopInterval', 'often');
        $service->setProperty('autoRestartInterval', 43200);

        try {
            $service->callProtected('checkEnvironment', [[]]);
            $this->fail('A non-numeric loop interval was accepted.');
        } catch (Exception $ex) {
            $this->assertTrue(strpos($ex->getMessage(), 'Invalid Loop Interval') !== false, $ex->getMessage());
        }
    }

    public function testPidIsReadBackFromThePidFile() {
        $service = $this->makeService();

        $this->assertFalse($service->getPidFromPidFile(), 'A missing pid file must not read as a pid.');

        file_put_contents($this->pidFile, '4242');
        $this->assertSame('4242', $service->getPidFromPidFile());
    }

    /**
     * isInvalidPid() is how a daemon notices the pid file no longer describes
     * it -- typically because a second copy was started and overwrote it.
     */
    public function testPidIsInvalidWhenTheFileNamesAnotherProcess() {
        $service = $this->makeService();
        $service->setProperty('pid', 4242);

        file_put_contents($this->pidFile, '4242');
        $this->assertFalse($service->isInvalidPid());

        file_put_contents($this->pidFile, '9999');
        $this->assertTrue($service->isInvalidPid());
    }

    public function testRuntimeCountsFromTheStartTime() {
        $service = $this->makeService();
        $service->setProperty('startTime', time() - 120);

        $this->assertEqualsWithDelta(120, $service->runtime(), 2);
    }
}
