<?php

class CConsole_Prompt_Themes_Default_PasswordPromptRenderer extends CConsole_Prompt_Themes_Default_Renderer {
    use CConsole_Prompt_Themes_Default_Concerns_DrawsBoxes;

    /**
     * Render the password prompt.
     *
     * @param CConsole_Prompt_PasswordPrompt $prompt
     *
     * @return string
     */
    public function __invoke($prompt) {
        $maxWidth = $prompt->terminal()->cols() - 6;

        if ($prompt->state === 'submit') {
            return $this->box(
                $this->dim($prompt->label),
                $this->truncate($prompt->masked(), $maxWidth)
            );
        }

        if ($prompt->state === 'cancel') {
            return $this->box(
                $this->truncate($prompt->label, $prompt->terminal()->cols() - 6),
                $this->strikethrough($this->dim($this->truncate($prompt->masked() ?: $prompt->placeholder, $maxWidth))),
                '',
                'red'
            )->error($prompt->cancelMessage);
        }

        if ($prompt->state === 'error') {
            return $this->box(
                $this->dim($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                $prompt->maskedWithCursor($maxWidth),
                '',
                'yellow'
            )->warning($this->truncate($prompt->error, $prompt->terminal()->cols() - 5));
        }

        return $this->box(
            $this->cyan($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
            $prompt->maskedWithCursor($maxWidth)
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
}
