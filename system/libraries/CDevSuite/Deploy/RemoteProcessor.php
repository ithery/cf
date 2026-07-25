<?php

/**
 * Description of RemoteProcessor
 */
use Symfony\Component\Process\Process;

abstract class CDevSuite_Deploy_RemoteProcessor {
    /**
     * Run the given task over SSH.
     *
     * @param CDevSuite_Deploy_Task $task
     * @param \Closure|null         $callback
     *
     * @return int
     */
    abstract public function run(CDevSuite_Deploy_Task $task, Closure $callback = null);

    /**
     * Get the configured server from the SSH config. Implemented by
     * CDevSuite_Deploy_Trait_ConfigurationParserTrait, which every concrete
     * subclass uses.
     *
     * @param string $host
     *
     * @return null|string
     */
    abstract protected function getConfiguredServer($host);

    /**
     * Run the given script on the given host.
     *
     * @param string                $host
     * @param CDevSuite_Deploy_Task $task
     *
     * @return array
     */
    protected function getProcess($host, CDevSuite_Deploy_Task $task) {
        $target = $this->getConfiguredServer($host) ?: $host;

        CDevSuite::info('Prepare script:' . PHP_EOL . $task->script . PHP_EOL . 'to:' . $target);

        $process = new CDevSuite_Deploy_Process(CDevSuite::ssh()->getRemoteSsh($target), $task);

        return [$target, $process->setTimeout(null)];
    }

    /**
     * Get the appropriate environment variables.
     *
     * @param string $host
     *
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
     * @param array $processes
     *
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
