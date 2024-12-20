<?php

use PHPUnit\Framework\Assert as PHPUnit;

class CServer_Process_Factory {
    use CTrait_Macroable {
        __call as macroCall;
    }

    /**
     * Indicates if the process factory has faked process handlers.
     *
     * @var bool
     */
    protected $recording = false;

    /**
     * All of the recorded processes.
     *
     * @var array
     */
    protected $recorded = [];

    /**
     * The registered fake handler callbacks.
     *
     * @var array
     */
    protected $fakeHandlers = [];

    /**
     * Indicates that an exception should be thrown if any process is not faked.
     *
     * @var bool
     */
    protected $preventStrayProcesses = false;

    /**
     * Create a new fake process response for testing purposes.
     *
     * @param array|string $output
     * @param array|string $errorOutput
     * @param int          $exitCode
     *
     * @return \CServer_Process_FakeProcessResult
     */
    public function result($output = '', $errorOutput = '', int $exitCode = 0) {
        return new CServer_Process_FakeProcessResult(
            '',
            $exitCode,
            $output,
            $errorOutput,
        );
    }

    /**
     * Begin describing a fake process lifecycle.
     *
     * @return \CServer_Process_FakeProcessDescription
     */
    public function describe() {
        return new CServer_Process_FakeProcessDescription();
    }

    /**
     * Begin describing a fake process sequence.
     *
     * @param array $processes
     *
     * @return \CServer_Process_FakeProcessSequence
     */
    public function sequence(array $processes = []) {
        return new CServer_Process_FakeProcessSequence($processes);
    }

    /**
     * Indicate that the process factory should fake processes.
     *
     * @param null|\Closure|array $callback
     *
     * @return $this
     */
    public function fake($callback = null) {
        $this->recording = true;

        if (is_null($callback)) {
            $this->fakeHandlers = ['*' => fn () => new CServer_Process_FakeProcessResult()];

            return $this;
        }

        if ($callback instanceof Closure) {
            $this->fakeHandlers = ['*' => $callback];

            return $this;
        }

        foreach ($callback as $command => $handler) {
            $this->fakeHandlers[is_numeric($command) ? '*' : $command] = $handler instanceof Closure
                    ? $handler
                    : fn () => $handler;
        }

        return $this;
    }

    /**
     * Determine if the process factory has fake process handlers and is recording processes.
     *
     * @return bool
     */
    public function isRecording() {
        return $this->recording;
    }

    /**
     * Record the given process if processes should be recorded.
     *
     * @param \CServer_Process_PendingProcess                  $process
     * @param \CServer_Process_Contract_ProcessResultInterface $result
     *
     * @return $this
     */
    public function recordIfRecording(CServer_Process_PendingProcess $process, CServer_Process_Contract_ProcessResultInterface $result) {
        if ($this->isRecording()) {
            $this->record($process, $result);
        }

        return $this;
    }

    /**
     * Record the given process.
     *
     * @param \CServer_Process_PendingProcess                  $process
     * @param \CServer_Process_Contract_ProcessResultInterface $result
     *
     * @return $this
     */
    public function record(CServer_Process_PendingProcess $process, CServer_Process_Contract_ProcessResultInterface $result) {
        $this->recorded[] = [$process, $result];

        return $this;
    }

    /**
     * Indicate that an exception should be thrown if any process is not faked.
     *
     * @param bool $prevent
     *
     * @return $this
     */
    public function preventStrayProcesses(bool $prevent = true) {
        $this->preventStrayProcesses = $prevent;

        return $this;
    }

    /**
     * Determine if stray processes are being prevented.
     *
     * @return bool
     */
    public function preventingStrayProcesses() {
        return $this->preventStrayProcesses;
    }

    /**
     * Assert that a process was recorded matching a given truth test.
     *
     * @param \Closure|string $callback
     *
     * @return $this
     */
    public function assertRan($callback) {
        $callback = is_string($callback) ? fn ($process) => $process->command === $callback : $callback;

        PHPUnit::assertTrue(
            c::collect($this->recorded)->filter(function ($pair) use ($callback) {
                return $callback($pair[0], $pair[1]);
            })->count() > 0,
            'An expected process was not invoked.'
        );

        return $this;
    }

    /**
     * Assert that a process was recorded a given number of times matching a given truth test.
     *
     * @param \Closure|string $callback
     * @param int             $times
     *
     * @return $this
     */
    public function assertRanTimes($callback, int $times = 1) {
        $callback = is_string($callback) ? fn ($process) => $process->command === $callback : $callback;

        $count = c::collect($this->recorded)->filter(function ($pair) use ($callback) {
            return $callback($pair[0], $pair[1]);
        })->count();

        PHPUnit::assertSame(
            $times,
            $count,
            "An expected process ran {$count} times instead of {$times} times."
        );
    }

    /**
     * Assert that a process was not recorded matching a given truth test.
     *
     * @param \Closure|string $callback
     *
     * @return $this
     */
    public function assertNotRan($callback) {
        $callback = is_string($callback) ? fn ($process) => $process->command === $callback : $callback;

        PHPUnit::assertTrue(
            c::collect($this->recorded)->filter(function ($pair) use ($callback) {
                return $callback($pair[0], $pair[1]);
            })->count() === 0,
            'An unexpected process was invoked.'
        );

        return $this;
    }

    /**
     * Assert that a process was not recorded matching a given truth test.
     *
     * @param \Closure|string $callback
     *
     * @return $this
     */
    public function assertDidntRun($callback) {
        return $this->assertNotRan($callback);
    }

    /**
     * Assert that no processes were recorded.
     *
     * @return $this
     */
    public function assertNothingRan() {
        PHPUnit::assertEmpty(
            $this->recorded,
            'An unexpected process was invoked.'
        );

        return $this;
    }

    /**
     * Start defining a pool of processes.
     *
     * @param callable $callback
     *
     * @return \CServer_Process_Pool
     */
    public function pool(callable $callback) {
        return new CServer_Process_Pool($this, $callback);
    }

    /**
     * Run a pool of processes and wait for them to finish executing.
     *
     * @param callable      $callback
     * @param null|callable $output
     *
     * @return \CServer_Process_ProcessPoolResults
     */
    public function concurrently(callable $callback, ?callable $output = null) {
        return (new CServer_Process_Pool($this, $callback))->start($output)->wait();
    }

    /**
     * Create a new pending process associated with this factory.
     *
     * @return \CServer_Process_PendingProcess
     */
    public function newPendingProcess() {
        return (new CServer_Process_PendingProcess($this))->withFakeHandlers($this->fakeHandlers);
    }

    /**
     * Dynamically proxy methods to a new pending process instance.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->newPendingProcess()->{$method}(...$parameters);
    }
}
