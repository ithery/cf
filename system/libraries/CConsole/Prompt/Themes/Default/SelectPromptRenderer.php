<?php

class CConsole_Prompt_Themes_Default_SelectPromptRenderer extends CConsole_Prompt_Themes_Default_Renderer implements CConsole_Prompt_Themes_Contracts_Scrolling {
    use CConsole_Prompt_Themes_Default_Concerns_DrawsBoxes;
    use CConsole_Prompt_Themes_Default_Concerns_DrawsScrollbars;

    /**
     * Render the select prompt.
     *
     * @param CConsole_Prompt_SelectPrompt $prompt
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
                'yellow'
            )->warning($this->truncate($prompt->error, $prompt->terminal()->cols() - 5));
        }

        return $this->box(
            $this->cyan($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
            $this->renderOptions($prompt),
            '',
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
        );
    }

    /**
     * Render the options.
     *
     * @param CConsole_Prompt_SelectPrompt $prompt
     *
     * @return string
     */
    protected function renderOptions($prompt) {
        $visible = $prompt->visible();

        $rendered = array_values(array_map(function ($label, $key) use ($prompt) {
            $label = $this->truncate($label, $prompt->terminal()->cols() - 12);

            $index = array_search($key, array_keys($prompt->options));

            if ($prompt->state === 'cancel') {
                return $this->dim($prompt->highlighted === $index
                    ? "› ● {$this->strikethrough($label)}  "
                    : "  ○ {$this->strikethrough($label)}  ");
            }

            return $prompt->highlighted === $index
                ? "{$this->cyan('›')} {$this->cyan('●')} {$label}  "
                : "  {$this->dim('○')} {$this->dim($label)}  ";
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
     * The number of lines to reserve outside of the scrollable area.
     *
     * @return int
     */
    public function reservedLines() {
        return 5;
    }
}
