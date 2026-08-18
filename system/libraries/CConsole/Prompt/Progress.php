<?php

class CConsole_Prompt_Progress extends CConsole_Prompt {
    /**
     * The current progress bar item count.
     *
     * @var int
     */
    public $progress = 0;

    /**
     * The total number of steps.
     *
     * @var int
     */
    public $total = 0;

    /**
     * The original value of pcntl_async_signals.
     *
     * @var bool
     */
    protected $originalAsync;

    /**
     * @var string
     */
    public $label;

    /**
     * @var iterable|int
     */
    public $steps;

    /**
     * @var string
     */
    public $hint;

    /**
     * Create a new ProgressBar instance.
     *
     * @param string        $label
     * @param iterable|int $steps
     * @param string        $hint
     */
    public function __construct($label, $steps, $hint = '') {
        $this->label = $label;
        $this->steps = $steps;
        $this->hint = $hint;

        if (is_int($this->steps)) {
            $this->total = $this->steps;
        } elseif (is_countable($this->steps)) {
            $this->total = count($this->steps);
        } elseif (is_iterable($this->steps)) {
            $this->total = iterator_count($this->steps);
        } else {
            throw new InvalidArgumentException('Unable to count steps.');
        }

        if ($this->total === 0) {
            throw new InvalidArgumentException('Progress bar must have at least one item.');
        }
    }

    /**
     * Map over the steps while rendering the progress bar.
     *
     * @param Closure $callback
     *
     * @throws Throwable
     *
     * @return array
     */
    public function map(Closure $callback) {
        $this->start();

        $result = [];

        try {
            if (is_int($this->steps)) {
                for ($i = 0; $i < $this->steps; $i++) {
                    $result[] = $callback($i, $this);
                    $this->advance();
                }
            } else {
                foreach ($this->steps as $step) {
                    $result[] = $callback($step, $this);
                    $this->advance();
                }
            }
        } catch (Throwable $e) {
            $this->state = 'error';
            $this->render();
            $this->restoreCursor();
            $this->resetSignals();

            throw $e;
        }

        if ($this->hint !== '') {
            // Just pause for one moment to show the final hint
            // so it doesn't look like it was skipped
            usleep(250000);
        }

        $this->finish();

        return $result;
    }

    /**
     * Start the progress bar.
     *
     * @return void
     */
    public function start() {
        $this->capturePreviousNewLines();

        if (function_exists('pcntl_signal')) {
            $this->originalAsync = pcntl_async_signals(true);
            pcntl_signal(SIGINT, function () {
                $this->state = 'cancel';
                $this->render();
                exit();
            });
        }

        $this->hideCursor();
        $this->render();
        $this->state = 'active';
    }

    /**
     * Advance the progress bar.
     *
     * @param int $step
     *
     * @return void
     */
    public function advance($step = 1) {
        $this->progress += $step;

        if ($this->progress > $this->total) {
            $this->progress = $this->total;
        }

        $this->render();
    }

    /**
     * Finish the progress bar.
     *
     * @return void
     */
    public function finish() {
        $this->state = 'submit';
        $this->render();
        $this->restoreCursor();
        $this->resetSignals();
    }

    /**
     * Force the progress bar to re-render.
     *
     * @return void
     */
    public function render() {
        parent::render();
    }

    /**
     * Update the label.
     *
     * @param string $label
     *
     * @return $this
     */
    public function label($label) {
        $this->label = $label;

        return $this;
    }

    /**
     * Update the hint.
     *
     * @param string $hint
     *
     * @return $this
     */
    public function hint($hint) {
        $this->hint = $hint;

        return $this;
    }

    /**
     * Get the completion percentage.
     *
     * @return float|int
     */
    public function percentage() {
        return $this->progress / $this->total;
    }

    /**
     * Disable prompting for input.
     *
     * @throws RuntimeException
     */
    public function prompt() {
        throw new RuntimeException('Progress Bar cannot be prompted.');
    }

    /**
     * Get the value of the prompt.
     *
     * @return bool
     */
    public function value() {
        return true;
    }

    /**
     * Reset the signal handling.
     *
     * @return void
     */
    protected function resetSignals() {
        if (isset($this->originalAsync)) {
            pcntl_async_signals($this->originalAsync);
            pcntl_signal(SIGINT, SIG_DFL);
        }
    }

    /**
     * Restore the cursor.
     */
    public function __destruct() {
        $this->restoreCursor();
    }
}
