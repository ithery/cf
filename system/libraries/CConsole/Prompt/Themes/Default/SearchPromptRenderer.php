<?php

class CConsole_Prompt_Themes_Default_SearchPromptRenderer extends CConsole_Prompt_Themes_Default_Renderer implements CConsole_Prompt_Themes_Contracts_Scrolling {
    use CConsole_Prompt_Themes_Default_Concerns_DrawsBoxes;
    use CConsole_Prompt_Themes_Default_Concerns_DrawsScrollbars;

    /**
     * Render the suggest prompt.
     *
     * @param CConsole_Prompt_SearchPrompt $prompt
     *
     * @return string
     */
    public function __invoke($prompt) {
        $maxWidth = $prompt->terminal()->cols() - 6;

        if ($prompt->state === 'submit') {
            return $this->box(
                $this->dim($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                $this->truncate($prompt->label(), $maxWidth)
            );
        }

        if ($prompt->state === 'cancel') {
            return $this->box(
                $this->dim($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                $this->strikethrough($this->dim($this->truncate($prompt->searchValue() ?: $prompt->placeholder, $maxWidth))),
                '',
                'red'
            )->error($prompt->cancelMessage);
        }

        if ($prompt->state === 'error') {
            return $this->box(
                $this->truncate($prompt->label, $prompt->terminal()->cols() - 6),
                $prompt->valueWithCursor($maxWidth),
                $this->renderOptions($prompt),
                'yellow'
            )->warning($this->truncate($prompt->error, $prompt->terminal()->cols() - 5));
        }

        if ($prompt->state === 'searching') {
            return $this->box(
                $this->cyan($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                $this->valueWithCursorAndSearchIcon($prompt, $maxWidth),
                $this->renderOptions($prompt),
                'gray',
                $prompt->infoText()
            )->hint($prompt->hint);
        }

        return $this->box(
            $this->cyan($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
            $prompt->valueWithCursor($maxWidth),
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
     * Render the value with the cursor and a search icon.
     *
     * @param CConsole_Prompt_SearchPrompt $prompt
     * @param int                           $maxWidth
     *
     * @return string
     */
    protected function valueWithCursorAndSearchIcon($prompt, $maxWidth) {
        return preg_replace(
            '/\s$/',
            $this->cyan('…'),
            $this->pad($prompt->valueWithCursor($maxWidth - 1) . '  ', min($this->longest($prompt->matches(), 2), $maxWidth))
        );
    }

    /**
     * Render a spacer to prevent jumping when the suggestions are displayed.
     *
     * @param CConsole_Prompt_SearchPrompt $prompt
     *
     * @return $this
     */
    protected function spaceForDropdown($prompt) {
        if ($prompt->searchValue() !== '') {
            return $this;
        }

        $this->newLine(max(
            0,
            min($prompt->scroll, $prompt->terminal()->lines() - 7) - count($prompt->matches())
        ));

        if ($prompt->matches() === []) {
            $this->newLine();
        }

        return $this;
    }

    /**
     * Render the options.
     *
     * @param CConsole_Prompt_SearchPrompt $prompt
     *
     * @return string
     */
    protected function renderOptions($prompt) {
        if ($prompt->searchValue() !== '' && empty($prompt->matches())) {
            return $this->gray('  ' . ($prompt->state === 'searching' ? 'Searching...' : 'No results.'));
        }

        $visible = $prompt->visible();

        $rendered = array_values(array_map(function ($label, $key) use ($prompt) {
            $label = $this->truncate($label, $prompt->terminal()->cols() - 10);

            $index = array_search($key, array_keys($prompt->matches()));

            return $prompt->highlighted === $index
                ? "{$this->cyan('›')} {$label}  "
                : "  {$this->dim($label)}  ";
        }, $visible, array_keys($visible)));

        return implode(PHP_EOL, $this->scrollbar(
            $rendered,
            $prompt->firstVisible,
            $prompt->scroll,
            count($prompt->matches()),
            min($this->longest($prompt->matches(), 4), $prompt->terminal()->cols() - 6)
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
