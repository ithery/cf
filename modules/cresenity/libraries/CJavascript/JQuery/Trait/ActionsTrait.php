<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 12:43:26 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CJavascript_JQuery_Trait_ActionsTrait {

    /**
     * add class to element
     *
     * @param string $element
     * @param string $class to add
     * @return string
     */
    public function addClass($element = 'this', $class = '') {

        return $this->genericCallValue('addClass', $element, $class);
    }

    /**
     * Insert content, specified by the parameter $element, to the end of each element in the set of matched elements $to.
     * @param string $to
     * @param string $element
     * @param boolean $immediatly defers the execution if set to false
     * @return string
     */
    public function append($to, $element) {

        return $this->genericCallElement('append', $to, $element);
    }

}
