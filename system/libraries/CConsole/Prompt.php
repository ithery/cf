<?php

use Symfony\Component\Console\Output\OutputInterface;

/**
 * @see https://github.com/laravel/prompts
 */
abstract class CConsole_Prompt {
    use CConsole_Prompt_Concerns_Colors;
    use CConsole_Prompt_Concerns_Cursor;
    use CConsole_Prompt_Concerns_Erase;
    use CConsole_Prompt_Concerns_Events;
    use CConsole_Prompt_Concerns_FakesInputOutput;
    use CConsole_Prompt_Concerns_Fallback;
    use CConsole_Prompt_Concerns_Interactivity;
    use CConsole_Prompt_Concerns_Themes;

    /**
     * The current state of the prompt.
     *
     * @var string
     */
    public $state = 'initial';

    /**
     * The error message from the validator.
     *
     * @var string
     */
    public $error = '';

    /**
     * The cancel message displayed when this prompt is cancelled.
     *
     * @var string
     */
    public $cancelMessage = 'Cancelled.';

    /**
     * The previously rendered frame.
     *
     * @var string
     */
    protected $prevFrame = '';

    /**
     * How many new lines were written by the last output.
     *
     * @var int
     */
    protected $newLinesWritten = 1;

    /**
     * Whether user input is required.
     *
     * @var bool|string
     */
    public $required;

    /**
     * The transformation callback.
     *
     * @var null|Closure
     */
    public $transform;

    /**
     * The validator callback or rules.
     *
     * @var mixed
     */
    public $validate;

    /**
     * The cancellation callback.
     *
     * @var null|Closure
     */
    protected static $cancelUsing;

    /**
     * Indicates if the prompt has been validated.
     *
     * @var bool
     */
    protected $validated = false;

    /**
     * The custom validation callback.
     *
     * @var null|Closure
     */
    protected static $validateUsing;

    /**
     * The revert handler from the StepBuilder.
     *
     * @var null|Closure
     */
    protected static $revertUsing;

    /**
     * The output instance.
     *
     * @var OutputInterface
     */
    protected static $output;

    /**
     * The terminal instance.
     *
     * @var CConsole_Prompt_Terminal
     */
    protected static $terminal;

    /**
     * Get the value of the prompt.
     *
     * @return mixed
     */
    abstract public function value();

    /**
     * Render the prompt and listen for input.
     *
     * @return mixed
     */
    public function prompt() {
        try {
            $this->capturePreviousNewLines();

            if (static::shouldFallback()) {
                return $this->fallback();
            }

            if (!isset(static::$interactive)) {
                static::$interactive = stream_isatty(STDIN);
            }

            if (!static::$interactive) {
                return $this->default();
            }

            $this->checkEnvironment();

            try {
                static::terminal()->setTty('-icanon -isig -echo');
            } catch (Throwable $e) {
                static::output()->writeln("<comment>{$e->getMessage()}</comment>");
                static::fallbackWhen(true);

                return $this->fallback();
            }

            $this->hideCursor();
            $this->render();

            $result = $this->runLoop(function ($key) {
                $continue = $this->handleKeyPress($key);

                $this->render();

                if ($continue === false || $key === CConsole_Prompt_Key::CTRL_C) {
                    if ($key === CConsole_Prompt_Key::CTRL_C) {
                        if (isset(static::$cancelUsing)) {
                            return CConsole_Prompt_Support_Result::from(call_user_func(static::$cancelUsing));
                        } else {
                            static::terminal()->exit();
                        }
                    }

                    if ($key === CConsole_Prompt_Key::CTRL_U && self::$revertUsing) {
                        throw new CConsole_Prompt_Exceptions_FormRevertedException();
                    }

                    return CConsole_Prompt_Support_Result::from($this->transformedValue());
                }

                // Continue looping.
                return null;
            });

            return $result;
        } finally {
            $this->clearListeners();
        }
    }

    /**
     * Implementation of the prompt looping mechanism.
     *
     * @param callable $callable callable(string $key): ?CConsole_Prompt_Support_Result
     *
     * @return mixed
     */
    public function runLoop(callable $callable) {
        while (($key = static::terminal()->read()) !== null) {
            /*
             * If $key is an empty string, Terminal::read
             * has failed. We can continue to the next
             * iteration of the loop, and try again.
             */
            if ($key === '') {
                continue;
            }

            $result = $callable($key);

            if ($result instanceof CConsole_Prompt_Support_Result) {
                return $result->value;
            }
        }
    }

    /**
     * Register a callback to be invoked when a user cancels a prompt.
     *
     * @param null|Closure $callback
     *
     * @return void
     */
    public static function cancelUsing($callback) {
        static::$cancelUsing = $callback;
    }

    /**
     * How many new lines were written by the last output.
     *
     * @return int
     */
    public function newLinesWritten() {
        return $this->newLinesWritten;
    }

    /**
     * Capture the number of new lines written by the last output.
     *
     * @return void
     */
    protected function capturePreviousNewLines() {
        $this->newLinesWritten = method_exists(static::output(), 'newLinesWritten')
            ? static::output()->newLinesWritten()
            : 1;
    }

    /**
     * Set the output instance.
     *
     * @param OutputInterface $output
     *
     * @return void
     */
    public static function setOutput(OutputInterface $output) {
        self::$output = $output;
    }

    /**
     * Get the current output instance.
     *
     * @return CConsole_Prompt_Output_ConsoleOutput
     */
    protected static function output() {
        if (!isset(self::$output)) {
            self::$output = new CConsole_Prompt_Output_ConsoleOutput();
        }

        return self::$output;
    }

    /**
     * Write output directly, bypassing newline capture.
     *
     * @param string $message
     *
     * @return void
     */
    protected static function writeDirectly($message) {
        if (method_exists(static::output(), 'writeDirectly')) {
            static::output()->writeDirectly($message);
        } elseif (method_exists(static::output(), 'getOutput')) {
            static::output()->getOutput()->write($message);
        } else {
            static::output()->write($message);
        }
    }

    /**
     * Get the terminal instance.
     *
     * @return CConsole_Prompt_Terminal
     */
    public static function terminal() {
        if (!isset(static::$terminal)) {
            static::$terminal = new CConsole_Prompt_Terminal();
        }

        return static::$terminal;
    }

    /**
     * Set the custom validation callback.
     *
     * @param Closure $callback
     *
     * @return void
     */
    public static function validateUsing(Closure $callback) {
        static::$validateUsing = $callback;
    }

    /**
     * Revert the prompt using the given callback.
     *
     * @internal
     *
     * @param Closure $callback
     *
     * @return void
     */
    public static function revertUsing(Closure $callback) {
        static::$revertUsing = $callback;
    }

    /**
     * Clear any previous revert callback.
     *
     * @internal
     *
     * @return void
     */
    public static function preventReverting() {
        static::$revertUsing = null;
    }

    /**
     * Render the prompt.
     *
     * @return void
     */
    protected function render() {
        $this->terminal()->initDimensions();

        $frame = $this->renderTheme();

        if ($frame === $this->prevFrame) {
            return;
        }

        if ($this->state === 'initial') {
            static::output()->write($frame);

            $this->state = 'active';
            $this->prevFrame = $frame;

            return;
        }

        $terminalHeight = $this->terminal()->lines();
        $previousFrameHeight = count(explode(PHP_EOL, $this->prevFrame));
        $renderableLines = array_slice(explode(PHP_EOL, $frame), abs(min(0, $terminalHeight - $previousFrameHeight)));

        $this->moveCursorToColumn(1);
        $this->moveCursorUp(min($terminalHeight, $previousFrameHeight) - 1);
        $this->eraseDown();
        $this->output()->write(implode(PHP_EOL, $renderableLines));

        $this->prevFrame = $frame;
    }

    /**
     * Submit the prompt.
     *
     * @return void
     */
    protected function submit() {
        $this->validate($this->transformedValue());

        if ($this->state !== 'error') {
            $this->state = 'submit';
        }
    }

    /**
     * Handle a key press and determine whether to continue.
     *
     * @param string $key
     *
     * @return bool
     */
    private function handleKeyPress($key) {
        if ($this->state === 'error') {
            $this->state = 'active';
        }

        $this->emit('key', $key);

        if ($this->state === 'submit') {
            return false;
        }

        if ($key === CConsole_Prompt_Key::CTRL_U) {
            if (!self::$revertUsing) {
                $this->state = 'error';
                $this->error = 'This cannot be reverted.';

                return true;
            }

            $this->state = 'cancel';
            $this->cancelMessage = 'Reverted.';

            call_user_func(self::$revertUsing);

            return false;
        }

        if ($key === CConsole_Prompt_Key::CTRL_C) {
            $this->state = 'cancel';

            return false;
        }

        if ($this->validated) {
            $this->validate($this->transformedValue());
        }

        return true;
    }

    /**
     * Transform the input.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    private function transform($value) {
        if (is_null($this->transform)) {
            return $value;
        }

        return call_user_func($this->transform, $value);
    }

    /**
     * Get the transformed value of the prompt.
     *
     * @return mixed
     */
    protected function transformedValue() {
        return $this->transform($this->value());
    }

    /**
     * Validate the input.
     *
     * @param mixed $value
     *
     * @return void
     */
    private function validate($value) {
        $this->validated = true;

        if ($this->required !== false && $this->isInvalidWhenRequired($value)) {
            $this->state = 'error';
            $this->error = is_string($this->required) && strlen($this->required) > 0 ? $this->required : 'Required.';

            return;
        }

        if (!isset($this->validate) && !isset(static::$validateUsing)) {
            return;
        }

        if (is_callable($this->validate)) {
            $error = call_user_func($this->validate, $value);
        } elseif (isset(static::$validateUsing)) {
            $error = call_user_func(static::$validateUsing, $this);
        } else {
            throw new RuntimeException('The validation logic is missing.');
        }

        if (!is_string($error) && !is_null($error)) {
            throw new RuntimeException('The validator must return a string or null.');
        }

        if (is_string($error) && strlen($error) > 0) {
            $this->state = 'error';
            $this->error = $error;
        }
    }

    /**
     * Determine whether the given value is invalid when the prompt is required.
     *
     * @param mixed $value
     *
     * @return bool
     */
    protected function isInvalidWhenRequired($value) {
        return $value === '' || $value === [] || $value === false || $value === null;
    }

    /**
     * Check whether the environment can support the prompt.
     *
     * @return void
     */
    private function checkEnvironment() {
        if (PHP_OS_FAMILY === 'Windows') {
            throw new RuntimeException('Prompts is not currently supported on Windows. Please use WSL or configure a fallback.');
        }
    }

    /**
     * Restore the cursor and terminal state.
     */
    public function __destruct() {
        $this->restoreCursor();

        static::terminal()->restoreTty();
    }
}
