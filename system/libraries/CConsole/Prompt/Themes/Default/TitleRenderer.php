<?php

class CConsole_Prompt_Themes_Default_TitleRenderer extends CConsole_Prompt_Themes_Default_Renderer {
    /**
     * Render the title.
     *
     * @param CConsole_Prompt_Title $title
     *
     * @return string
     */
    public function __invoke($title) {
        return "\033]0;{$title->title}\007";
    }
}
