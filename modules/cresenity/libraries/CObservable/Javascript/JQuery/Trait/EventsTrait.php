<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 5:12:31 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CObservable_Javascript_JQuery_Trait_EventsTrait {

    /**
     * @return string
     */
    abstract function getSelector();

    abstract function resetJQueryStatement();

    abstract function jQueryStatement();

    public function onClick($statements, $options = array()) {
        $retFalse = carr::get($options, 'retFalse', true);
        $preventDefault = carr::get($options, 'preventDefault', FALSE);
        $stopPropagation = carr::get($options, 'stopPropagation', false);
        if ($statements instanceof CJavascript_Statement) {
            $statements = $statements->getStatement();
        }
        $statement = $this->jQueryStatement()->event('click', $statements, $options);
        $this->resetJQueryStatement();
        return $this;
    }

    public function onChange($statements, $options = array()) {
        $retFalse = carr::get($options, 'retFalse', true);
        $preventDefault = carr::get($options, 'preventDefault', FALSE);
        $stopPropagation = carr::get($options, 'stopPropagation', false);
       

        $statement = $this->jQueryStatement()->event('change', $statements, $options);

        $this->resetJQueryStatement();
        return $this;
    }

}
