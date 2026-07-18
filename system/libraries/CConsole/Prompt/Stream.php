<?php

class CConsole_Prompt_Stream extends CConsole_Prompt {
    use CConsole_Prompt_Themes_Default_Concerns_InteractsWithStrings;

    /**
     * @var int
     */
    protected $minWidth = 0;

    /**
     * @var string
     */
    protected $message = '';

    /**
     * @var array<int, string>
     */
    protected $currentlyFading = [];

    /**
     * @var int
     */
    protected $maxWidth = 0;

    /**
     * @var array<int, Closure>
     */
    protected $fadingOutColors = [];

    /**
     * Create a new Stream instance.
     */
    public function __construct() {
        $this->maxWidth = static::terminal()->cols() - 20;
        $this->hideCursor();
        $this->fadingOutColors = $this->fadeOut();
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function append($message) {
        $this->currentlyFading[] = $message;

        while (count($this->currentlyFading) > count($this->fadingOutColors)) {
            $this->message .= array_shift($this->currentlyFading);
        }

        $this->render();

        return $this;
    }

    /**
     * @return void
     */
    public function close() {
        try {
            while (count($this->currentlyFading) > 0) {
                $this->message .= array_shift($this->currentlyFading);
                $this->render();
                usleep(25000);
            }
        } finally {
            $this->showCursor();
        }
    }

    /**
     * @return array<int, string>
     */
    public function lines() {
        $toFadeIn = [];

        foreach ($this->currentlyFading as $index => $message) {
            $toFadeIn[] = call_user_func($this->fadingOutColors[$index], $message);
        }

        $lines = explode(PHP_EOL, $this->message . implode('', $toFadeIn));
        $finalLines = [];

        foreach ($lines as $line) {
            $finalLines = array_merge(
                $finalLines,
                $this->ansiWordwrap($line, $this->maxWidth)
            );
        }

        return $finalLines;
    }

    /**
     * @return mixed
     */
    public function prompt() {
        throw new RuntimeException('Stream cannot be prompted');
    }

    /**
     * Get the value of the prompt.
     *
     * @return string
     */
    public function value() {
        return $this->message . implode('', $this->currentlyFading);
    }

    /**
     * Get an array of closures that progressively fade text from full color to nearly invisible.
     *
     * @param int $steps
     *
     * @return array<int, Closure>
     */
    protected function fadeOut($steps = 10) {
        if (!static::terminal()->supportsTrueColor()) {
            return [
                function ($text) {
                    return $text;
                },
                function ($text) {
                    return $this->dim($text);
                },
            ];
        }

        $fg = static::terminal()->foregroundColor();
        $bg = static::terminal()->backgroundColor();

        return array_map(
            function ($step) use ($fg, $bg, $steps) {
                $factor = 1 - ($step / $steps);
                $r = (int) ($bg[0] + ($fg[0] - $bg[0]) * $factor);
                $g = (int) ($bg[1] + ($fg[1] - $bg[1]) * $factor);
                $b = (int) ($bg[2] + ($fg[2] - $bg[2]) * $factor);

                return function ($text) use ($r, $g, $b) {
                    return "\e[38;2;{$r};{$g};{$b}m{$text}\e[0m";
                };
            },
            range(0, $steps - 1)
        );
    }
}
