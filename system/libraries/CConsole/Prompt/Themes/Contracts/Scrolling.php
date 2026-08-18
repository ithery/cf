<?php

interface CConsole_Prompt_Themes_Contracts_Scrolling {
    /**
     * The number of lines to reserve outside of the scrollable area.
     *
     * @return int
     */
    public function reservedLines();
}
