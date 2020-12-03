<?php

/**
 * Description of RemoteProcessor
 *
 * @author Hery
 */
use Symfony\Component\Process\Process;

abstract class CDevSuite_Deploy_RemoteProcessor {

    /**
     * Run the given task over SSH.
     *
     * @param  CDevSuite_Deploy_Task  $task
     * @param  \Closure|null  $callback
     * @return int
     */
    abstract public function run(CDevSuite_Deploy_Task $task, Closure $callback = null);

    /**
     * Run the given script on the given host.
     *
     * @param  string  $host
     * @param  CDevSuite_Deploy_Task  $task
     * @return array
     */
    protected function getProcess($host, CDevSuite_Deploy_Task $task) {
        $target = $this->getConfiguredServer($host) ?: $host;

        $env = $this->getEnvironment($host);
        CDevSuite::info('Prepare script:'.PHP_EOL.$task->script.PHP_EOL.'to:'.$target);

        if (in_array($target, ['local', 'localhost', '127.0.0.1'])) {
            $process = Process::fromShellCommandline($task->script, null, $env);
        }

        // Here we'll run the SSH task on the server inline. We do not need to write the
        // script out to a file or anything. We will start the SSH process then pass
        // these lines of output back to the parent callback for display purposes.
        else {
            $delimiter = 'EOF-DEVSUITE-DEPLOY';

            foreach ($env as $k => $v) {
                if ($v !== false) {
                    $env[$k] = 'export ' . $k . '="' . $v . '"' . PHP_EOL;
                }
            }

            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $process = Process::fromShellCommandline("ssh $target -T");

                $process->setInput(
                        implode(PHP_EOL, $env)
                        . 'set -e ' . PHP_EOL
                        . str_replace("\r", '', $task->script)
                );
            } else {
                $process = Process::fromShellCommandline(
                                "ssh $target 'bash -se' << \\$delimiter" . PHP_EOL
                                . implode(PHP_EOL, $env) . PHP_EOL
                                . 'set -e' . PHP_EOL
                                . $task->script . PHP_EOL
                                . $delimiter
                );
            }
        }

        return [$target, $process->setTimeout(null)];
    }

    /**
     * Get the appropriate environment variables.
     *
     * @param  string  $host
     * @return array
     */
    protected function getEnvironment($host) {
        return [
            'DEVSUITE_HOST' => $host,
        ];
    }

    /**
     * Gather the cumulative exit code for the processes.
     *
     * @param  array  $processes
     * @return int
     */
    protected function gatherExitCodes(array $processes) {
        $code = 0;

        foreach ($processes as $process) {
            $code = $code + $process->getExitCode();
        }

        return $code;
    }

}
