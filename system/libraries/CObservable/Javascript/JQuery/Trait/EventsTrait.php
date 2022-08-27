<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 2, 2018, 5:12:31 PM
 */
trait CObservable_Javascript_JQuery_Trait_EventsTrait {
    /**
     * @return string
     */
    abstract public function getSelector();

    abstract public function resetJQueryStatement();

    /**
     * @return CJavascript_Statement_JQuery
     */
    abstract public function jQueryStatement();

    public function onClick($statements, $options = []) {
        $retFalse = carr::get($options, 'retFalse', true);
        $preventDefault = carr::get($options, 'preventDefault', false);
        $stopPropagation = carr::get($options, 'stopPropagation', false);
        if ($statements instanceof CJavascript_Statement) {
            $statements = $statements->getStatement();
        }
        $statement = $this->jQueryStatement()->event('click', $statements, $options);
        $this->resetJQueryStatement();

        return $this;
    }


    public function onHover($statements, $options = []) {
        $retFalse = carr::get($options, 'retFalse', true);
        $preventDefault = carr::get($options, 'preventDefault', false);
        $stopPropagation = carr::get($options, 'stopPropagation', false);
        if ($statements instanceof CJavascript_Statement) {
            $statements = $statements->getStatement();
        }
        $statement = $this->jQueryStatement()->event('hover', $statements, $options);
        $this->resetJQueryStatement();

        return $this;
    }

    public function onMouseEnter($statements, $options = []) {
        $retFalse = carr::get($options, 'retFalse', true);
        $preventDefault = carr::get($options, 'preventDefault', false);
        $stopPropagation = carr::get($options, 'stopPropagation', false);
        if ($statements instanceof CJavascript_Statement) {
            $statements = $statements->getStatement();
        }
        $statement = $this->jQueryStatement()->event('mouseenter', $statements, $options);
        $this->resetJQueryStatement();

        return $this;
    }

    public function onMouseLeave($statements, $options = []) {
        $retFalse = carr::get($options, 'retFalse', true);
        $preventDefault = carr::get($options, 'preventDefault', false);
        $stopPropagation = carr::get($options, 'stopPropagation', false);
        if ($statements instanceof CJavascript_Statement) {
            $statements = $statements->getStatement();
        }
        $statement = $this->jQueryStatement()->event('mouseleave', $statements, $options);
        $this->resetJQueryStatement();

        return $this;
    }

    public function onChange($statements, $options = []) {
        $retFalse = carr::get($options, 'retFalse', true);
        $preventDefault = carr::get($options, 'preventDefault', false);
        $stopPropagation = carr::get($options, 'stopPropagation', false);

        $statement = $this->jQueryStatement()->event('change', $statements, $options);

        $this->resetJQueryStatement();

        return $this;
    }
}
