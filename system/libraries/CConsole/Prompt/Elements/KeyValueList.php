<?php

class CConsole_Prompt_Elements_KeyValueList implements CConsole_Prompt_Elements_ElementContract {
    /**
     * @var array<string, string>
     */
    public $items;

    /**
     * @param array<string, string> $items
     */
    public function __construct(array $items) {
        $this->items = $items;
    }
}
