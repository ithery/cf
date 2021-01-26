<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
class CComponnent_CreateBladeView extends CComponent {

    public static function fromString($contents) {
        return (new static)->createBladeViewFromString(CView_Factory::instance(), $contents);
    }

    public function render() {
        
    }

}
