<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 29, 2020
 */
class CComponent_CreateBladeView extends CComponent {
    public static function fromString($contents) {
        return (new static)->createBladeViewFromString(CView_Factory::instance(), $contents);
    }

    public function render() {
    }
}
