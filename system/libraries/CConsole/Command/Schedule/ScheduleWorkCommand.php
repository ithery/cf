<?php
use Symfony\Component\Process\Process;

class CConsole_Command_Schedule_ScheduleWorkCommand extends CConsole_Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'schedule:work';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the schedule worker';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {
        $this->info('Schedule worker started successfully.');

        list($lastExecutionStartedAt, $keyOfLastExecutionWithOutput, $executions) = [null, null, []];

        while (true) {
            usleep(100 * 1000);

            if (CCarbon::now()->second === 0
                && !CCarbon::now()->startOfMinute()->equalTo($lastExecutionStartedAt)
            ) {
                $executions[] = $execution = new Process([PHP_BINARY, 'cf', 'schedule:run']);

                $execution->start();

                $lastExecutionStartedAt = CCarbon::now()->startOfMinute();
            }

            foreach ($executions as $key => $execution) {
                $output = trim($execution->getIncrementalOutput())
                          . trim($execution->getIncrementalErrorOutput());

                if (!empty($output)) {
                    if ($key !== $keyOfLastExecutionWithOutput) {
                        $this->info(PHP_EOL . '[' . date('c') . '] Execution #' . ($key + 1) . ' output:');

                        $keyOfLastExecutionWithOutput = $key;
                    }

                    $this->output->writeln($output);
                }

                if (!$execution->isRunning()) {
                    unset($executions[$key]);
                }
            }
        }
    }
}
