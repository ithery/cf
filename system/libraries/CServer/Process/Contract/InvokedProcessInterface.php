<?php

interface CServer_Process_Contract_InvokedProcessInterface
{
    /**
     * Get the process ID if the process is still running.
     *
     * @return int|null
     */
    public function id();

    /**
     * Send a signal to the process.
     *
     * @param  int  $signal
     * @return $this
     */
    public function signal(int $signal);

    /**
     * Determine if the process is still running.
     *
     * @return bool
     */
    public function running();

    /**
     * Get the standard output for the process.
     *
     * @return string
     */
    public function output();

    /**
     * Get the error output for the process.
     *
     * @return string
     */
    public function errorOutput();

    /**
     * Get the latest standard output for the process.
     *
     * @return string
     */
    public function latestOutput();

    /**
     * Get the latest error output for the process.
     *
     * @return string
     */
    public function latestErrorOutput();

    /**
     * Wait for the process to finish.
     *
     * @param  callable|null  $output
     * @return \CConsole_Process_ProcessResult
     */
    public function wait(callable $output = null);
}
