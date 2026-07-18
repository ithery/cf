<?php

class CConsole_Prompt_Elements_BulletedList implements CConsole_Prompt_Elements_ElementContract {
    /**
     * @var array<int, string>
     */
    public $items;

    /**
     * @var bool
     */
    public $spaced;

    /**
     * @param array<int, string> $items
     * @param bool                $spaced
     */
    public function __construct(array $items, $spaced = false) {
        $this->items = $items;
        $this->spaced = $spaced;
    }
}
