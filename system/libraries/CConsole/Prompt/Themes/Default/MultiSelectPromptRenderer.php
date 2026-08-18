<?php

class CConsole_Prompt_Themes_Default_MultiSelectPromptRenderer extends CConsole_Prompt_Themes_Default_Renderer implements CConsole_Prompt_Themes_Contracts_Scrolling {
    use CConsole_Prompt_Themes_Default_Concerns_DrawsBoxes;
    use CConsole_Prompt_Themes_Default_Concerns_DrawsScrollbars;

    /**
     * Render the multiselect prompt.
     *
     * @param CConsole_Prompt_MultiSelectPrompt $prompt
     *
     * @return string
     */
    public function __invoke($prompt) {
        if ($prompt->state === 'submit') {
            return $this->box(
                $this->dim($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                $this->renderSelectedOptions($prompt)
            );
        }

        if ($prompt->state === 'cancel') {
            return $this->box(
                $this->truncate($prompt->label, $prompt->terminal()->cols() - 6),
                $this->renderOptions($prompt),
                '',
                'red'
            )->error($prompt->cancelMessage);
        }

        if ($prompt->state === 'error') {
            return $this->box(
                $this->truncate($prompt->label, $prompt->terminal()->cols() - 6),
                $this->renderOptions($prompt),
                '',
                'yellow',
                count($prompt->options) > $prompt->scroll ? (count($prompt->value()) . ' selected') : ''
            )->warning($this->truncate($prompt->error, $prompt->terminal()->cols() - 5));
        }

        return $this->box(
            $this->cyan($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
            $this->renderOptions($prompt),
            '',
            'gray',
            $this->getInfoText($prompt)
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
     * Render the options.
     *
     * @param CConsole_Prompt_MultiSelectPrompt $prompt
     *
     * @return string
     */
    protected function renderOptions($prompt) {
        $visible = $prompt->visible();

        $rendered = array_values(array_map(function ($label, $key) use ($prompt) {
            $label = $this->truncate($label, $prompt->terminal()->cols() - 12);

            $index = array_search($key, array_keys($prompt->options));
            $active = $index === $prompt->highlighted;
            if (carr::isList($prompt->options)) {
                $value = $prompt->options[$index];
            } else {
                $value = array_keys($prompt->options)[$index];
            }
            $selected = in_array($value, $prompt->value());

            if ($prompt->state === 'cancel') {
                if ($active && $selected) {
                    return $this->dim("› ◼ {$this->strikethrough($label)}  ");
                }
                if ($active) {
                    return $this->dim("› ◻ {$this->strikethrough($label)}  ");
                }
                if ($selected) {
                    return $this->dim("  ◼ {$this->strikethrough($label)}  ");
                }

                return $this->dim("  ◻ {$this->strikethrough($label)}  ");
            }

            if ($active && $selected) {
                return "{$this->cyan('› ◼')} {$label}  ";
            }
            if ($active) {
                return "{$this->cyan('›')} ◻ {$label}  ";
            }
            if ($selected) {
                return "  {$this->cyan('◼')} {$this->dim($label)}  ";
            }

            return "  {$this->dim('◻')} {$this->dim($label)}  ";
        }, $visible, array_keys($visible)));

        return implode(PHP_EOL, $this->scrollbar(
            $rendered,
            $prompt->firstVisible,
            $prompt->scroll,
            count($prompt->options),
            min($this->longest($prompt->options, 6), $prompt->terminal()->cols() - 6),
            $prompt->state === 'cancel' ? 'dim' : 'cyan'
        ));
    }

    /**
     * Render the selected options.
     *
     * @param CConsole_Prompt_MultiSelectPrompt $prompt
     *
     * @return string
     */
    protected function renderSelectedOptions($prompt) {
        if (count($prompt->labels()) === 0) {
            return $this->gray('None');
        }

        return implode("\n", array_map(
            function ($label) use ($prompt) {
                return $this->truncate($label, $prompt->terminal()->cols() - 6);
            },
            $prompt->labels()
        ));
    }

    /**
     * Render the info text.
     *
     * @param CConsole_Prompt_MultiSelectPrompt $prompt
     *
     * @return string
     */
    protected function getInfoText($prompt) {
        $parts = array_filter([
            $prompt->infoText(),
            count($prompt->options) > $prompt->scroll ? (count($prompt->value()) . ' selected') : '',
        ]);

        return implode(' · ', $parts);
    }

    /**
     * The number of lines to reserve outside of the scrollable area.
     *
     * @return int
     */
    public function reservedLines() {
        return 5;
    }
}
