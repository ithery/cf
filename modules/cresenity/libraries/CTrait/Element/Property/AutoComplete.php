<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 24, 2018, 7:15:05 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * @author Hery Kurniawan
 * @since Jun 3, 2018, 2:38:26 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Property_AutoComplete {

    /**
     *
     * @var string 
     */
    public $autoComplete;

    /**
     * 
     * @param string|bool $bool
     * @return $this
     */
    public function setAutoComplete($bool) {
        if (is_string($bool)) {
            $bool = in_array($bool, array("on", "yes", "ok", "y")) ? true : false;
        }
        $this->autoComplete = $bool;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getAutoComplete() {
        return $this->autoComplete;
    }

    /**
     * 
     * @return $this
     */
    public function setAutoCompleteOn() {
        $this->autoComplete = true;
        return $this;
    }

    /**
     * 
     * @return $this
     */
    public function setAutoCompleteOff() {
        $this->autoComplete = false;
        return $this;
    }

}
