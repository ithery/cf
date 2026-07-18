<?php

class CConsole_Prompt_Elements_Heading implements CConsole_Prompt_Elements_ElementContract {
    /**
     * @var string
     */
    public $text;

    /**
     * @param string $text
     */
    public function __construct($text) {
        $this->text = $text;
    }
}
