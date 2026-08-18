<?php

class CConsole_Prompt_Themes_Default_SuggestPromptRenderer extends CConsole_Prompt_Themes_Default_Renderer implements CConsole_Prompt_Themes_Contracts_Scrolling {
    use CConsole_Prompt_Themes_Default_Concerns_DrawsBoxes;
    use CConsole_Prompt_Themes_Default_Concerns_DrawsScrollbars;

    /**
     * Render the suggest prompt.
     *
     * @param CConsole_Prompt_SuggestPrompt $prompt
     *
     * @return string
     */
    public function __invoke($prompt) {
        $maxWidth = $prompt->terminal()->cols() - 6;

        if ($prompt->state === 'submit') {
            return $this->box(
                $this->dim($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                $this->truncate($prompt->value(), $maxWidth)
            );
        }

        if ($prompt->state === 'cancel') {
            return $this->box(
                $this->dim($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                $this->strikethrough($this->dim($this->truncate($prompt->value() ?: $prompt->placeholder, $maxWidth))),
                '',
                'red'
            )->error($prompt->cancelMessage);
        }

        if ($prompt->state === 'error') {
            return $this->box(
                $this->truncate($prompt->label, $prompt->terminal()->cols() - 6),
                $this->valueWithCursorAndArrow($prompt, $maxWidth),
                $this->renderOptions($prompt),
                'yellow'
            )->warning($this->truncate($prompt->error, $prompt->terminal()->cols() - 5));
        }

        return $this->box(
            $this->cyan($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
            $this->valueWithCursorAndArrow($prompt, $maxWidth),
            $this->renderOptions($prompt),
            'gray',
            $prompt->infoText()
        )->when(
            $prompt->hint,
            function () use ($prompt) {
                $this->hint($prompt->hint);
            },
            function () {
                $this->newLine(); // Space for errors
            }
        )->spaceForDropdown($prompt);
    }

    /**
     * Render the value with the cursor and an arrow.
     *
     * @param CConsole_Prompt_SuggestPrompt $prompt
     * @param int                            $maxWidth
     *
     * @return string
     */
    protected function valueWithCursorAndArrow($prompt, $maxWidth) {
        if ($prompt->highlighted !== null || $prompt->value() !== '' || count($prompt->matches()) === 0) {
            return $prompt->valueWithCursor($maxWidth);
        }

        return preg_replace(
            '/\s$/',
            $this->cyan('⌄'),
            $this->pad($prompt->valueWithCursor($maxWidth - 1) . '  ', min($this->longest($prompt->matches(), 2), $maxWidth))
        );
    }

    /**
     * Render a spacer to prevent jumping when the suggestions are displayed.
     *
     * @param CConsole_Prompt_SuggestPrompt $prompt
     *
     * @return $this
     */
    protected function spaceForDropdown($prompt) {
        if ($prompt->value() === '' && $prompt->highlighted === null) {
            $this->newLine(min(
                count($prompt->matches()),
                $prompt->scroll,
                $prompt->terminal()->lines() - 7
            ) + 1);
        }

        return $this;
    }

    /**
     * Render the options.
     *
     * @param CConsole_Prompt_SuggestPrompt $prompt
     *
     * @return string
     */
    protected function renderOptions($prompt) {
        if (empty($prompt->matches()) || ($prompt->value() === '' && $prompt->highlighted === null)) {
            return '';
        }

        $visible = $prompt->visible();

        $rendered = array_map(function ($label, $key) use ($prompt) {
            $label = $this->truncate($label, $prompt->terminal()->cols() - 12);

            return $prompt->highlighted === $key
                ? "{$this->cyan('›')} {$label}  "
                : "  {$this->dim($label)}  ";
        }, $visible, array_keys($visible));

        return implode(PHP_EOL, $this->scrollbar(
            $rendered,
            $prompt->firstVisible,
            $prompt->scroll,
            count($prompt->matches()),
            min($this->longest($prompt->matches(), 4), $prompt->terminal()->cols() - 6),
            $prompt->state === 'cancel' ? 'dim' : 'cyan'
        ));
    }

    /**
     * The number of lines to reserve outside of the scrollable area.
     *
     * @return int
     */
    public function reservedLines() {
        return 7;
    }
}
