<?php
use Symfony\Component\Process\Process;

class CQC_Testing_Daemon_QueueRunner extends CDaemon_ServiceAbstract {
    protected $loopInterval = 0.5;

    protected $lastActiveTime;

    protected $isIdle;

    /**
     * @var CQC_Executor
     */
    protected $executor;

    public function setup() {
        $this->lastActiveTime = c::now();
        c::db()->disableBenchmark();
        $this->executor = CQC::createExecutor();
    }

    private function repository() {
        return CQC::manager()->testing()->repository();
    }

    public function execute() {
        if (!$this->test()) {
            if (!$this->isIdle) {
                $this->isIdle = true;

                $this->log('idle...');
            }
        } else {
            $this->isIdle = false;
        }
        if (c::now()->diffInHours($this->lastActiveTime) > 1) {
            $this->log('Stopping...');
            $this->stop();
        }
    }

    /**
     * Find and execute a test.
     */
    private function test() {
        if (!$test = $this->repository()->getNextTestFromQueue()) {
            return false;
        }

        $ok = false;

        $lines = '';

        $run = $this->repository()->markTestAsRunning($test);

        //$this->log('Testing...' . $test->getFile());

        //$this->log('RUNNING: ' . $command . ' - at ' . $test->getFile() . ' - cwd:' . getcwd(), 'comment');

        $logOutput = '';

        for ($times = 0; $times <= $test->suite->retries; $times++) {
            if ($times > 0) {
                $this->log('retrying...');
            }
            $cfCli = CConsole::kernel()->cfCli();

            $this->log('command:' . 'test ' . $test->getFile());
            $exitCode = $cfCli->call('test ' . $test->getFile());
            $lines = $cfCli->output();
            // $process = $this->executor->exec($command, null, function ($type, $buffer) use ($run, $test, &$logOutput) {
            //     $logOutput .= $buffer;

            //     $this->repository()->updateRunLog($run, $this->getOutput($this->dataRepository->formatLog($logOutput, $test), $test));

            //     $this->log($buffer);
            // });

            //$lines = $this->getOutput($process, $test);
            $this->log($this->repository()->formatLog($lines, $test));
            if ($ok = $this->testPassed($exitCode, $test)) {
                break;
            }
        }

        $this->log($ok ? 'OK' : 'FAILED');
        $this->log('Updating Result..');
        $this->repository()->storeTestResult($run, $test, $lines, $ok, $this->executor->startedAt, $this->executor->endedAt);
        $this->log('Result Updated');

        return true;
    }

    /**
     * Get the output from pipe or Process.
     *
     * @param $buffer \Symfony\Component\Process\Process
     *
     * @return bool|string
     */
    private function getOutput($buffer) {
        $buffer = $buffer instanceof Process ? $buffer->getOutput() : $buffer;

        return $buffer;
    }

    /**
     * Check if the test has passed.
     *
     * @param $exitCode
     * @param \CQC_Testing_Model_Test $test
     *
     * @return bool
     */
    private function testPassed($exitCode, CQC_Testing_Model_Test $test) {
        if ($exitCode !== 0) {
            return false;
        }

        return true;
    }
}
