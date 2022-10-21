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

        $command = CConsole::kernel()->cfCli()->call(CConsole_Command_TestCommand::class, $test->file);

        chdir($test->suite->path);

        $this->log('RUNNING: ' . $command . ' - at ' . $test->suite->project->path . ' - cwd:' . getcwd(), 'comment');

        $logOutput = '';

        for ($times = 0; $times <= $test->suite->retries; $times++) {
            if ($times > 0) {
                $this->log('retrying...');
            }

            $process = $this->executor->exec($command, $test->suite->project->path, function ($type, $buffer) use ($run, $test, &$logOutput) {
                $logOutput .= $buffer;

                $this->repository()->updateRunLog($run, $this->getOutput($this->dataRepository->formatLog($logOutput, $test), $test));

                $this->log($buffer);
            });

            $lines = $this->getOutput($process, $test);

            if ($ok = $this->testPassed($process->getExitCode(), $test)) {
                break;
            }
        }

        $this->log($ok ? 'OK' : 'FAILED');

        $this->repository()->storeTestResult($run, $test, $lines, $ok, $this->executor->startedAt, $this->executor->endedAt);

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
