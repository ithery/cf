<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 6:38:10 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CRenderable_Observable_JQuery_Trait_ActionsTrait {

    /**
     * @return CJavascript_JQuery
     */
    abstract function getObject();

    /**
     * @return string
     */
    abstract function getSelector();

    /**
     * 
     * @param string $class
     */
    public function addClass($class = '') {
        $this->getObject()->addClass($this->getSelector(), $class);
    }

    public function after($element) {
        $this->getObject()->after($this->getSelector(), $element);
    }

    public function before($element) {
        $this->getObject()->before($this->getSelector(), $element);
    }

    public function prepend($element) {
        $this->getObject()->prepend($this->getSelector(), $element);
    }

    public function append($element) {
        if ($element instanceOf CRenderable_Observable) {
            $element->jquery()->detach();
            $element->jquery()->appendTo($this->getSelector());
        } else {
            $this->getObject()->append($this->getSelector(), $element);
        }
    }

    public function val($value=null) {
        $this->getObject()->val($this->getSelector(),$value);
    }
    
    public function html($element) {
        $this->getObject()->html($this->getSelector(), $element);
    }

    public function remove() {
        $this->getObject()->remove($this->getSelector());
    }

    public function detach() {
        $this->getObject()->detach($this->getSelector());
    }

    public function appendTo(CRenderable_Observable $to) {

        $this->getObject()->appendTo($this->getSelector(), $to->jquery()->getSelector());
    }

}
