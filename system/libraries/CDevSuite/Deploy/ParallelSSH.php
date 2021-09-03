<?php

/**
 * Description of ParallelSSH
 *
 * @author Hery
 */
use Symfony\Component\Process\Process;

class CDevSuite_Deploy_ParallelSSH extends CDevSuite_Deploy_RemoteProcessor {

    use CDevSuite_Deploy_ConfigurationParserTrait;

    /**
     * Run the given task over SSH.
     *
     * @param  CDevSuite_Deploy_Task  $task
     * @param  \Closure|null  $callback
     * @return int
     */
    public function run(CDevSuite_Deploy_Task $task, Closure $callback = null) {
        $processes = [];

        $callback = $callback ?: function () {
            
        };

        // Here we will gather all the process instances by host. We will build them in
        // an array so we can easily loop through them then start them up. We'll key
        // the array by the target name and set the value as the process instance.
        foreach ($task->hosts as $host) {
            $process = $this->getProcess($host, $task);

            $processes[$process[0]] = $process[1];
        }

        // Next we will start all of the processes, but we won't block. Instead we will
        // gather all of the output incrementally from each of the processes as they
        // stay running on a machine. We'll check to see if any are still running.
        $this->startProcesses($processes);

        while ($this->areRunning($processes)) {
            $this->gatherOutput($processes, $callback);
        }

        // Finally, we'll gather the output one last time to make sure no more output is
        // sitting on the buffer. Then we'll gather the cumulative exit code just to
        // see if we need to run the after even for the task or if we skip it out.
        $this->gatherOutput($processes, $callback);

        return $this->gatherExitCodes($processes);
    }

    /**
     * Start all of the processes.
     *
     * @param  array  $processes
     * @return void
     */
    protected function startProcesses(array $processes) {
        foreach ($processes as $process) {
            $process->start();
        }
    }

    /**
     * Determine if any of the processes are running.
     *
     * @param  array  $processes
     * @return bool
     */
    protected function areRunning(array $processes) {
        foreach ($processes as $process) {
            if ($process->isRunning()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gather the output from all of the processes.
     *
     * @param  array  $processes
     * @param  \Closure  $callback
     * @return void
     */
    protected function gatherOutput(array $processes, Closure $callback) {
        foreach ($processes as $host => $process) {
            $methods = [
                Process::OUT => 'getIncrementalOutput',
                Process::ERR => 'getIncrementalErrorOutput',
            ];

            foreach ($methods as $type => $method) {
                $output = $process->{$method}();

                if (!empty($output)) {
                    $callback($type, $host, $output);
                }
            }
        }
    }

}
