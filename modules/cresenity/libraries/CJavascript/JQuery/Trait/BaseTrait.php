<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 12:45:44 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CJavascript_JQuery_Trait_BaseTrait {

    public function getSelector($element) {
        if ($element instanceOf CRenderable) {
            return '#' . $element->id();
        }
        return $element;
    }

}
