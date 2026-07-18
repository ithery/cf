<?php

class CConsole_Prompt_Themes_Default_SpinnerRenderer extends CConsole_Prompt_Themes_Default_Renderer {
    use CConsole_Prompt_Concerns_HasSpinner;

    /**
     * Render the spinner.
     *
     * @param CConsole_Prompt_Spinner $spinner
     *
     * @return string
     */
    public function __invoke($spinner) {
        if ($spinner->static) {
            return $this->line(" {$this->cyan($this->staticFrame)} {$spinner->message}");
        }

        $spinner->interval = $this->interval;

        return $this->line(" {$this->cyan($this->spinnerFrame($spinner->count))} {$spinner->message}");
    }
}
