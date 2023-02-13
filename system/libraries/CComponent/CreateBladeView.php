<?php

defined('SYSPATH') or die('No direct access allowed.');

class CComponent_CreateBladeView extends CComponent {
    public static function fromString($contents) {
        return (new static())->createBladeViewFromString(CView_Factory::instance(), $contents);
    }

    public function render() {
    }
}
