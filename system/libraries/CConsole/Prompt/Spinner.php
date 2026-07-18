<?php

class CConsole_Prompt_Spinner extends CConsole_Prompt {
    /**
     * How long to wait between rendering each frame.
     *
     * @var int
     */
    public $interval = 100;

    /**
     * The number of times the spinner has been rendered.
     *
     * @var int
     */
    public $count = 0;

    /**
     * Whether the spinner can only be rendered once.
     *
     * @var bool
     */
    public $static = false;

    /**
     * The process ID after forking.
     *
     * @var int
     */
    protected $pid;

    /**
     * @var string
     */
    public $message;

    /**
     * Create a new Spinner instance.
     *
     * @param string $message
     */
    public function __construct($message = '') {
        $this->message = $message;
    }

    /**
     * Render the spinner and execute the callback.
     *
     * @param Closure $callback
     *
     * @return mixed
     */
    public function spin(Closure $callback) {
        $this->capturePreviousNewLines();

        if (!static::output()->isDecorated() || !(function_exists('pcntl_fork') && function_exists('posix_kill'))) {
            return $this->renderStatically($callback);
        }

        $originalAsync = pcntl_async_signals(true);

        pcntl_signal(SIGINT, function () {
            exit();
        });

        try {
            $this->hideCursor();
            $this->render();

            $this->pid = pcntl_fork();

            if ($this->pid === 0) {
                while (true) {
                    $this->render();

                    $this->count++;

                    usleep($this->interval * 1000);
                }
            } else {
                $result = $callback();

                $this->resetTerminal($originalAsync);

                return $result;
            }
        } catch (Throwable $e) {
            $this->resetTerminal($originalAsync);

            throw $e;
        }
    }

    /**
     * Reset the terminal.
     *
     * @param bool $originalAsync
     *
     * @return void
     */
    protected function resetTerminal($originalAsync) {
        pcntl_async_signals($originalAsync);
        pcntl_signal(SIGINT, SIG_DFL);

        $this->eraseRenderedLines();
    }

    /**
     * Render a static version of the spinner.
     *
     * @param Closure $callback
     *
     * @return mixed
     */
    protected function renderStatically(Closure $callback) {
        $this->static = true;

        try {
            $this->hideCursor();
            $this->render();

            $result = $callback();
        } finally {
            $this->eraseRenderedLines();
        }

        return $result;
    }

    /**
     * Disable prompting for input.
     *
     * @throws RuntimeException
     */
    public function prompt() {
        throw new RuntimeException('Spinner cannot be prompted.');
    }

    /**
     * Get the current value of the prompt.
     *
     * @return bool
     */
    public function value() {
        return true;
    }

    /**
     * Clear the lines rendered by the spinner.
     *
     * @return void
     */
    protected function eraseRenderedLines() {
        $lines = explode(PHP_EOL, $this->prevFrame);
        $this->moveCursor(-999, -count($lines) + 1);
        $this->eraseDown();
    }

    /**
     * Clean up after the spinner.
     */
    public function __destruct() {
        if (!empty($this->pid)) {
            posix_kill($this->pid, SIGHUP);
        }

        parent::__destruct();
    }
}
