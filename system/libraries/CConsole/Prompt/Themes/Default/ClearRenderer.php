<?php

class CConsole_Prompt_Themes_Default_ClearRenderer extends CConsole_Prompt_Themes_Default_Renderer {
    /**
     * Clear the terminal.
     *
     * @return string
     */
    public function __invoke() {
        return "\033[H\033[J";
    }
}
