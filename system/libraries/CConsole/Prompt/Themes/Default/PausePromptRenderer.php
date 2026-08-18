<?php

class CConsole_Prompt_Themes_Default_PausePromptRenderer extends CConsole_Prompt_Themes_Default_Renderer {
    use CConsole_Prompt_Themes_Default_Concerns_DrawsBoxes;

    /**
     * Render the pause prompt.
     *
     * @param CConsole_Prompt_PausePrompt $prompt
     *
     * @return $this
     */
    public function __invoke($prompt) {
        $lines = explode(PHP_EOL, $prompt->message);

        $color = $prompt->state === 'submit' ? 'green' : 'gray';

        foreach ($lines as $line) {
            $this->line(" {$this->{$color}($line)}");
        }

        return $this;
    }
}
