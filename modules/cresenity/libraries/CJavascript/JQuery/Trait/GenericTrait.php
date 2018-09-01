<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 12:49:09 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CJavascript_JQuery_Trait_GenericTrait {

    /**
     * Execute a generic jQuery call with a value.
     * @param string $jQueryCall
     * @param string $element
     * @param string $param
     * @param boolean $immediatly delayed if false
     */
    public function genericCallValue($jQueryCall, $element = 'this', $param = "") {
        $element = $this->getSelector($element);
        $element = CJavascript_Helper_Javascript::prepElement($element);
        if (isset($param)) {
            $param = CJavascript_Helper_Javascript::prepValue($param);
            $str = "$({$element}).{$jQueryCall}({$param});";
        } else
            $str = "$({$element}).{$jQueryCall}();";

        $this->codeForCompile[] = $str;
        return $str;
    }

    /**
     * Execute a generic jQuery call with 2 elements.
     * @param string $jQueryCall
     * @param string $to
     * @param string $element
     * @param boolean $immediatly delayed if false
     * @return string
     */
    public function genericCallElement($jQueryCall, $to = 'this', $element) {
        $to = $this->getSelector($to);
        $to = CJavascript_Helper_Javascript::prepElement($to);
        $element = CJavascript_Helper_Javascript::prepElement($element);
        $str = "$({$to}).{$jQueryCall}({$element});";

        $this->codeForCompile[] = $str;
        return $str;
    }

}
