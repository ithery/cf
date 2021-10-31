<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 2, 2018, 12:45:44 AM
 */
trait CJavascript_JQuery_Trait_BaseTrait {
    public function getSelector($element) {
        if ($element instanceof CRenderable) {
            return '#' . $element->id();
        }
        return $element;
    }

    /**
     * Ensures the speed parameter is valid for jQuery
     *
     * @param string|int $speed
     *
     * @return string
     */
    private function validateSpeed($speed) {
        if (in_array($speed, ['slow', 'normal', 'fast'])) {
            $speed = '"' . $speed . '"';
        } elseif (preg_match('/[^0-9]/', $speed)) {
            $speed = '';
        }
        return $speed;
    }

    /**
     * Allows to attach a condition
     *
     * @param string $condition
     * @param string $jsCodeIfTrue
     * @param string $jsCodeIfFalse
     */
    public function condition($condition, $jsCodeIfTrue, $jsCodeIfFalse = null) {
        $str = 'if(' . $condition . '){' . $jsCodeIfTrue . '}';
        if (isset($jsCodeIfFalse)) {
            $str .= 'else{' . $jsCodeIfFalse . '}';
        }

        $this->addScript($str);
        return $str;
    }
}
