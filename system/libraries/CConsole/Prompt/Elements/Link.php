<?php

class CConsole_Prompt_Elements_Link implements CConsole_Prompt_Elements_ElementContract {
    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $label;

    /**
     * @var bool
     */
    public $underline;

    /**
     * @param string      $url
     * @param null|string $label
     * @param bool        $underline
     */
    public function __construct($url, $label = null, $underline = true) {
        $this->url = $url;
        $this->underline = $underline;
        $this->label = $label === null ? $url : $label;
    }

    /**
     * @return string
     */
    public function __toString() {
        $text = ($this->underline) ? "\e[4m{$this->label}\e[24m" : $this->label;

        return "\e]8;;{$this->url}\e\\{$text}\e]8;;\e\\";
    }
}
