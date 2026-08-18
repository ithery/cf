<?php

class CConsole_Prompt_Themes_Default_ConfirmPromptRenderer extends CConsole_Prompt_Themes_Default_Renderer {
    use CConsole_Prompt_Themes_Default_Concerns_DrawsBoxes;

    /**
     * Render the confirm prompt.
     *
     * @param CConsole_Prompt_ConfirmPrompt $prompt
     *
     * @return string
     */
    public function __invoke($prompt) {
        if ($prompt->state === 'submit') {
            return $this->box(
                $this->dim($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                $this->truncate($prompt->label(), $prompt->terminal()->cols() - 6)
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
            $this->renderOptions($prompt)
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
     * Render the confirm prompt options.
     *
     * @param CConsole_Prompt_ConfirmPrompt $prompt
     *
     * @return string
     */
    protected function renderOptions($prompt) {
        $length = (int) floor(($prompt->terminal()->cols() - 14) / 2);
        $yes = $this->truncate($prompt->yes, $length);
        $no = $this->truncate($prompt->no, $length);

        if ($prompt->state === 'cancel') {
            return $this->dim($prompt->confirmed
                ? "● {$this->strikethrough($yes)} / ○ {$this->strikethrough($no)}"
                : "○ {$this->strikethrough($yes)} / ● {$this->strikethrough($no)}");
        }

        return $prompt->confirmed
            ? "{$this->green('●')} {$yes} {$this->dim('/ ○ ' . $no)}"
            : "{$this->dim('○ ' . $yes . ' /')} {$this->green('●')} {$no}";
    }
}
