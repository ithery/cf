<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 5:12:31 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CRenderable_Observable_JQuery_Trait_EventsTrait {

    /**
     * @return CJavascript_JQuery
     */
    abstract function getObject();

    /**
     * @return string
     */
    abstract function getSelector();

    public function onClick($js, $options = array()) {
        $retFalse = carr::get($options, 'retFalse', true);
        $preventDefault = carr::get($options, 'preventDefault', FALSE);
        $stopPropagation = carr::get($options, 'stopPropagation', false);

        $this->getObject()->onClick($this->getSelector(), $js, $retFalse, $preventDefault, $stopPropagation);
    }

    public function onChange($js, $options = array()) {
        $retFalse = carr::get($options, 'retFalse', true);
        $preventDefault = carr::get($options, 'preventDefault', FALSE);
        $stopPropagation = carr::get($options, 'stopPropagation', false);

        $this->getObject()->onChange($this->getSelector(), $js, $retFalse, $preventDefault, $stopPropagation);
    }

}
