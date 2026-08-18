<?php

class CConsole_Prompt_Themes_Default_NumberPromptRenderer extends CConsole_Prompt_Themes_Default_Renderer {
    use CConsole_Prompt_Themes_Default_Concerns_DrawsBoxes;

    /**
     * @var string
     */
    protected $upArrow = '▲';

    /**
     * @var string
     */
    protected $downArrow = '▼';

    /**
     * Render the number prompt.
     *
     * @param CConsole_Prompt_NumberPrompt $prompt
     *
     * @return string
     */
    public function __invoke($prompt) {
        $maxWidth = $prompt->terminal()->cols() - 6;

        if ($prompt->state === 'submit') {
            return $this->box(
                $this->dim($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                $this->truncate((string) $prompt->value(), $maxWidth)
            );
        }

        if ($prompt->state === 'cancel') {
            return $this->box(
                $this->truncate($prompt->label, $prompt->terminal()->cols() - 6),
                $this->strikethrough($this->dim($this->truncate((string) $prompt->value() ?: $prompt->placeholder, $maxWidth))),
                '',
                'red'
            )->error('Cancelled.');
        }

        if ($prompt->state === 'error') {
            return $this->box(
                $this->truncate($prompt->label, $prompt->terminal()->cols() - 6),
                $this->withArrows($prompt, $prompt->valueWithCursor($maxWidth), 'yellow'),
                '',
                'yellow'
            )->warning($this->truncate($prompt->error, $prompt->terminal()->cols() - 5));
        }

        return $this->box(
            $this->cyan($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
            $this->withArrows($prompt, $prompt->valueWithCursor($maxWidth))
        )->when(
            $prompt->hint,
            function () use ($prompt) {
                $this->hint($prompt->hint);
            },
            function () {
                $this->newLine(); // Space for errors
            }
        );
    }

    /**
     * @param CConsole_Prompt_NumberPrompt $prompt
     * @param int|string                    $value
     * @param null|string                   $color
     *
     * @return string
     */
    protected function withArrows($prompt, $value, $color = null) {
        $arrows = $this->getArrows($prompt, $color);
        $valueLength = mb_strwidth($this->stripEscapeSequences((string) $value));
        $padding = $this->minWidth - $valueLength - mb_strwidth($this->stripEscapeSequences($arrows));

        return $value . str_repeat(' ', $padding) . $arrows;
    }

    /**
     * @param CConsole_Prompt_NumberPrompt $prompt
     * @param null|string                   $color
     *
     * @return string
     */
    protected function getArrows($prompt, $color = null) {
        $upArrow = $this->upArrow;
        $downArrow = $this->downArrow;

        if ($color) {
            $upArrow = $this->{$color}($upArrow);
            $downArrow = $this->{$color}($downArrow);
        }

        if (is_numeric($prompt->value())) {
            if ((int) $prompt->value() === $prompt->min) {
                $downArrow = $this->dim($downArrow);
            }

            if ((int) $prompt->value() === $prompt->max) {
                $upArrow = $this->dim($upArrow);
            }

            return $upArrow . $downArrow;
        }

        if ($prompt->value() === '') {
            return $upArrow . $downArrow;
        }

        return $this->dim($upArrow) . $this->dim($downArrow);
    }
}
