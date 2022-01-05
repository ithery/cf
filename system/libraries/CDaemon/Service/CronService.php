<?php
use Symfony\Component\Process\Process;

class CDaemon_Service_CronService extends CDaemon_ServiceAbstract {
    protected $loopInterval = 0.1;

    protected $lastExecutionStartedAt;

    protected $keyOfLastExecutionWithOutput;

    protected $executions;

    public function setup() {
        $this->lastExecutionStartedAt = null;
        $this->keyOfLastExecutionWithOutput = null;
        $this->executions = [];
        $this->log('Cron service started successfully.');
    }

    public function execute() {
        if (CCarbon::now()->second === 0
            && !CCarbon::now()->startOfMinute()->equalTo($this->lastExecutionStartedAt)
        ) {
            $this->executions[] = $execution = new Process([PHP_BINARY, 'cf', 'cron:run']);

            $execution->start();

            $this->lastExecutionStartedAt = CCarbon::now()->startOfMinute();
        }

        foreach ($this->executions as $key => $execution) {
            $output = trim($execution->getIncrementalOutput())
                          . trim($execution->getIncrementalErrorOutput());

            if (!empty($output)) {
                if ($key !== $this->keyOfLastExecutionWithOutput) {
                    $this->log('Execution #' . ($key + 1) . ' output:');

                    $this->keyOfLastExecutionWithOutput = $key;
                }

                $this->log($output);
            }

            if (!$execution->isRunning()) {
                unset($this->executions[$key]);
            }
        }
    }
}
