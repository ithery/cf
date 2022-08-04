<?php

trait CTrait_Element_Property_Shortcut {
    protected $shortcut;

    public function setShortcut($shortcut) {
        $this->shortcut = $shortcut;
    }

    public function getShortcut() {
        return $this->shortcut;
    }
}
