<?php

abstract class CConsole_Prompt_Themes_Default_Renderer {
    use CConsole_Prompt_Concerns_Colors;
    use CConsole_Prompt_Concerns_Truncation;

    /**
     * The output to be rendered.
     *
     * @var string
     */
    protected $output = '';

    /**
     * @var CConsole_Prompt
     */
    protected $prompt;

    /**
     * Create a new renderer instance.
     *
     * @param CConsole_Prompt $prompt
     */
    public function __construct($prompt) {
        $this->prompt = $prompt;
    }

    /**
     * Render a line of output.
     *
     * @param string $message
     *
     * @return $this
     */
    protected function line($message) {
        $this->output .= $message . PHP_EOL;

        return $this;
    }

    /**
     * Render a new line.
     *
     * @param int $count
     *
     * @return $this
     */
    protected function newLine($count = 1) {
        $this->output .= str_repeat(PHP_EOL, $count);

        return $this;
    }

    /**
     * Render a warning message.
     *
     * @param string $message
     *
     * @return $this
     */
    protected function warning($message) {
        return $this->line($this->yellow("  ⚠ {$message}"));
    }

    /**
     * Render an error message.
     *
     * @param string $message
     *
     * @return $this
     */
    protected function error($message) {
        return $this->line($this->red("  ⚠ {$message}"));
    }

    /**
     * Render an hint message.
     *
     * @param string $message
     *
     * @return $this
     */
    protected function hint($message) {
        if ($message === '') {
            return $this;
        }

        $message = $this->truncate($message, $this->prompt->terminal()->cols() - 6);

        return $this->line($this->gray("  {$message}"));
    }

    /**
     * Apply the callback if the given "value" is truthy.
     *
     * @param mixed         $value
     * @param callable      $callback
     * @param null|callable $default
     *
     * @return $this
     */
    protected function when($value, callable $callback, $default = null) {
        if ($value) {
            $callback($this);
        } elseif ($default) {
            $default($this);
        }

        return $this;
    }

    /**
     * Render the output with a blank line above and below.
     *
     * @return string
     */
    public function __toString() {
        return str_repeat(PHP_EOL, max(2 - $this->prompt->newLinesWritten(), 0))
            . $this->output
            . (in_array($this->prompt->state, ['submit', 'cancel']) ? PHP_EOL : '');
    }
}
