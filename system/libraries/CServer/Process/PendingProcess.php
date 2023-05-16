<?php

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessTimedOutException as SymfonyTimeoutException;

class CServer_Process_PendingProcess {
    /**
     * The command to invoke the process.
     *
     * @var null|array<array-key, string>|string
     */
    public $command;

    /**
     * The working directory of the process.
     *
     * @var null|string
     */
    public $path;

    /**
     * The maximum number of seconds the process may run.
     *
     * @var null|int
     */
    public $timeout = 60;

    /**
     * The maximum number of seconds the process may go without returning output.
     *
     * @var int
     */
    public $idleTimeout;

    /**
     * The additional environment variables for the process.
     *
     * @var array
     */
    public $environment = [];

    /**
     * The standard input data that should be piped into the command.
     *
     * @var null|string|int|float|bool|resource|\Traversable
     */
    public $input;

    /**
     * Indicates whether output should be disabled for the process.
     *
     * @var bool
     */
    public $quietly = false;

    /**
     * Indicates if TTY mode should be enabled.
     *
     * @var bool
     */
    public $tty = false;

    /**
     * The options that will be passed to "proc_open".
     *
     * @var array
     */
    public $options = [];

    /**
     * The process factory instance.
     *
     * @var \CServer_Process_Factory
     */
    protected $factory;

    /**
     * The registered fake handler callbacks.
     *
     * @var array
     */
    protected $fakeHandlers = [];

    /**
     * Create a new pending process instance.
     *
     * @param \CServer_Process_Factory $factory
     *
     * @return void
     */
    public function __construct(CServer_Process_Factory $factory) {
        $this->factory = $factory;
    }

    /**
     * Specify the command that will invoke the process.
     *
     * @param array<array-key, string>|string $command
     *
     * @return $this
     */
    public function command($command) {
        $this->command = $command;

        return $this;
    }

    /**
     * Specify the working directory of the process.
     *
     * @param string $path
     *
     * @return $this
     */
    public function path(string $path) {
        $this->path = $path;

        return $this;
    }

    /**
     * Specify the maximum number of seconds the process may run.
     *
     * @param int $timeout
     *
     * @return $this
     */
    public function timeout(int $timeout) {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Specify the maximum number of seconds a process may go without returning output.
     *
     * @param int $timeout
     *
     * @return $this
     */
    public function idleTimeout(int $timeout) {
        $this->idleTimeout = $timeout;

        return $this;
    }

    /**
     * Indicate that the process may run forever without timing out.
     *
     * @return $this
     */
    public function forever() {
        $this->timeout = null;

        return $this;
    }

    /**
     * Set the additional environent variables for the process.
     *
     * @param array $environment
     *
     * @return $this
     */
    public function env(array $environment) {
        $this->environment = $environment;

        return $this;
    }

    /**
     * Set the standard input that should be provided when invoking the process.
     *
     * @param null|\Traversable|resource|string|int|float|bool $input
     *
     * @return $this
     */
    public function input($input) {
        $this->input = $input;

        return $this;
    }

    /**
     * Disable output for the process.
     *
     * @return $this
     */
    public function quietly() {
        $this->quietly = true;

        return $this;
    }

    /**
     * Enable TTY mode for the process.
     *
     * @param bool $tty
     *
     * @return $this
     */
    public function tty(bool $tty = true) {
        $this->tty = $tty;

        return $this;
    }

    /**
     * Set the "proc_open" options that should be used when invoking the process.
     *
     * @param array $options
     *
     * @return $this
     */
    public function options(array $options) {
        $this->options = $options;

        return $this;
    }

    /**
     * Run the process.
     *
     * @param null|array<array-key, string>|string $command
     * @param null|callable                        $output
     *
     * @return \CServer_Process_Contract_ProcessResultInterface
     */
    public function run($command = null, callable $output = null) {
        $this->command = $command ?: $this->command;

        try {
            $process = $this->toSymfonyProcess($command);

            if ($fake = $this->fakeFor($command = $process->getCommandline())) {
                return c::tap($this->resolveSynchronousFake($command, $fake), function ($result) {
                    $this->factory->recordIfRecording($this, $result);
                });
            } elseif ($this->factory->isRecording() && $this->factory->preventingStrayProcesses()) {
                throw new RuntimeException('Attempted process [' . $command . '] without a matching fake.');
            }

            return new CServer_Process_ProcessResult(c::tap($process)->run($output));
        } catch (SymfonyTimeoutException $e) {
            throw new CServer_Process_Exception_ProcessTimedOutException($e, new CServer_Process_ProcessResult($process));
        }
    }

    /**
     * Start the process in the background.
     *
     * @param null|array<array-key, string>|string $command
     * @param callable                             $output
     *
     * @return \CServer_Process_InvokedProcess
     */
    public function start($command = null, callable $output = null) {
        $this->command = $command ?: $this->command;

        $process = $this->toSymfonyProcess($command);

        if ($fake = $this->fakeFor($command = $process->getCommandline())) {
            return c::tap($this->resolveAsynchronousFake($command, $output, $fake), function (CServer_Process_FakeInvokedProcess $process) {
                $this->factory->recordIfRecording($this, $process->predictProcessResult());
            });
        } elseif ($this->factory->isRecording() && $this->factory->preventingStrayProcesses()) {
            throw new RuntimeException('Attempted process [' . $command . '] without a matching fake.');
        }

        return new CServer_Process_InvokedProcess(c::tap($process)->start($output));
    }

    /**
     * Get a Symfony Process instance from the current pending command.
     *
     * @param null|array<array-key, string>|string $command
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function toSymfonyProcess($command) {
        $command = $command ?? $this->command;

        $process = is_iterable($command)
                ? new Process($command, null, $this->environment)
                : Process::fromShellCommandline((string) $command, null, $this->environment);

        $process->setWorkingDirectory((string) ($this->path ?? getcwd()));
        $process->setTimeout($this->timeout);

        if ($this->idleTimeout) {
            $process->setIdleTimeout($this->idleTimeout);
        }

        if ($this->input) {
            $process->setInput($this->input);
        }

        if ($this->quietly) {
            $process->disableOutput();
        }

        if ($this->tty) {
            $process->setTty(true);
        }

        if (!empty($this->options)) {
            $process->setOptions($this->options);
        }

        return $process;
    }

    /**
     * Specify the fake process result handlers for the pending process.
     *
     * @param array $fakeHandlers
     *
     * @return $this
     */
    public function withFakeHandlers(array $fakeHandlers) {
        $this->fakeHandlers = $fakeHandlers;

        return $this;
    }

    /**
     * Get the fake handler for the given command, if applicable.
     *
     * @param string $command
     *
     * @return null|\Closure
     */
    protected function fakeFor(string $command) {
        return c::collect($this->fakeHandlers)
            ->first(fn ($handler, $pattern) => cstr::is($pattern, $command));
    }

    /**
     * Resolve the given fake handler for a synchronous process.
     *
     * @param string   $command
     * @param \Closure $fake
     *
     * @return mixed
     */
    protected function resolveSynchronousFake($command, Closure $fake) {
        $result = $fake($this);

        if (is_string($result) || is_array($result)) {
            return (new CServer_Process_FakeProcessResult('', 0, $result))->withCommand($command);
        } elseif ($result instanceof CServer_Process_ProcessResult) {
            return $result;
        } elseif ($result instanceof CServer_Process_FakeProcessResult) {
            return $result->withCommand($command);
        } elseif ($result instanceof CServer_Process_FakeProcessDescription) {
            return $result->toProcessResult($command);
        } elseif ($result instanceof CServer_Process_FakeProcessSequence) {
            return $this->resolveSynchronousFake($command, fn () => $result());
        }

        throw new LogicException('Unsupported synchronous process fake result provided.');
    }

    /**
     * Resolve the given fake handler for an asynchronous process.
     *
     * @param string        $command
     * @param null|callable $output
     * @param \Closure      $fake
     *
     * @return \CServer_Process_FakeInvokedProcess
     */
    protected function resolveAsynchronousFake($command, $output, Closure $fake) {
        $result = $fake($this);

        if (is_string($result) || is_array($result)) {
            $result = new CServer_Process_FakeProcessResult('', 0, $result);
        }

        if ($result instanceof CServer_Process_ProcessResult) {
            return (new CServer_Process_FakeInvokedProcess(
                $command,
                (new CServer_Process_FakeProcessDescription())
                    ->replaceOutput($result->output())
                    ->replaceErrorOutput($result->errorOutput())
                    ->runsFor(0)
                    ->exitCode($result->exitCode())
            ))->withOutputHandler($output);
        } elseif ($result instanceof CServer_Process_FakeProcessResult) {
            return (new CServer_Process_FakeInvokedProcess(
                $command,
                (new CServer_Process_FakeProcessDescription())
                    ->replaceOutput($result->output())
                    ->replaceErrorOutput($result->errorOutput())
                    ->runsFor(0)
                    ->exitCode($result->exitCode())
            ))->withOutputHandler($output);
        } elseif ($result instanceof CServer_Process_FakeProcessDescription) {
            return (new CServer_Process_FakeInvokedProcess($command, $result))->withOutputHandler($output);
        } elseif ($result instanceof CServer_Process_FakeProcessSequence) {
            return $this->resolveAsynchronousFake($command, $output, fn () => $result());
        }

        throw new LogicException('Unsupported asynchronous process fake result provided.');
    }
}
