<?php

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessTimedOutException as SymfonyTimeoutException;

class CServer_Process_InvokedProcess implements CServer_Process_Contract_InvokedProcessInterface {
    /**
     * The underlying process instance.
     *
     * @var \Symfony\Component\Process\Process
     */
    protected $process;

    /**
     * Create a new invoked process instance.
     *
     * @param \Symfony\Component\Process\Process $process
     *
     * @return void
     */
    public function __construct(Process $process) {
        $this->process = $process;
    }

    /**
     * Get the process ID if the process is still running.
     *
     * @return null|int
     */
    public function id() {
        return $this->process->getPid();
    }

    /**
     * Send a signal to the process.
     *
     * @param int $signal
     *
     * @return $this
     */
    public function signal(int $signal) {
        $this->process->signal($signal);

        return $this;
    }

    /**
     * Determine if the process is still running.
     *
     * @return bool
     */
    public function running() {
        return $this->process->isRunning();
    }

    /**
     * Get the standard output for the process.
     *
     * @return string
     */
    public function output() {
        return $this->process->getOutput();
    }

    /**
     * Get the error output for the process.
     *
     * @return string
     */
    public function errorOutput() {
        return $this->process->getErrorOutput();
    }

    /**
     * Get the latest standard output for the process.
     *
     * @return string
     */
    public function latestOutput() {
        return $this->process->getIncrementalOutput();
    }

    /**
     * Get the latest error output for the process.
     *
     * @return string
     */
    public function latestErrorOutput() {
        return $this->process->getIncrementalErrorOutput();
    }

    /**
     * Wait for the process to finish.
     *
     * @param null|callable $output
     *
     * @return \CServer_Process_ProcessResult
     */
    public function wait(callable $output = null) {
        try {
            $this->process->wait($output);

            return new CServer_Process_ProcessResult($this->process);
        } catch (SymfonyTimeoutException $e) {
            throw new CServer_Process_Exception_ProcessTimedOutException($e, new CServer_Process_ProcessResult($this->process));
        }
    }
}
