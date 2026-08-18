<?php

class CConsole_Prompt_Themes_Default_TextareaPromptRenderer extends CConsole_Prompt_Themes_Default_Renderer implements CConsole_Prompt_Themes_Contracts_Scrolling {
    use CConsole_Prompt_Themes_Default_Concerns_DrawsBoxes;
    use CConsole_Prompt_Themes_Default_Concerns_DrawsScrollbars;

    /**
     * Render the textarea prompt.
     *
     * @param CConsole_Prompt_TextareaPrompt $prompt
     *
     * @return string
     */
    public function __invoke($prompt) {
        $prompt->width = $prompt->terminal()->cols() - 8;

        if ($prompt->state === 'submit') {
            return $this->box(
                $this->dim($this->truncate($prompt->label, $prompt->width)),
                implode(PHP_EOL, $prompt->lines())
            );
        }

        if ($prompt->state === 'cancel') {
            $lines = array_map(function ($line) {
                return $this->strikethrough($this->dim($line));
            }, $prompt->lines());

            return $this->box(
                $this->truncate($prompt->label, $prompt->width),
                implode(PHP_EOL, $lines),
                '',
                'red'
            )->error($prompt->cancelMessage);
        }

        if ($prompt->state === 'error') {
            return $this->box(
                $this->truncate($prompt->label, $prompt->width),
                $this->renderText($prompt),
                '',
                'yellow',
                'Ctrl+D to submit'
            )->warning($this->truncate($prompt->error, $prompt->terminal()->cols() - 5));
        }

        return $this->box(
            $this->cyan($this->truncate($prompt->label, $prompt->width)),
            $this->renderText($prompt),
            '',
            'gray',
            'Ctrl+D to submit'
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
     * Render the text in the prompt.
     *
     * @param CConsole_Prompt_TextareaPrompt $prompt
     *
     * @return string
     */
    protected function renderText($prompt) {
        $visible = $prompt->visible();

        while (count($visible) < $prompt->scroll) {
            $visible[] = '';
        }

        $longest = $this->longest($prompt->lines()) + 2;

        return implode(PHP_EOL, $this->scrollbar(
            $visible,
            $prompt->firstVisible,
            $prompt->scroll,
            count($prompt->lines()),
            min($longest, $prompt->width + 2)
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
