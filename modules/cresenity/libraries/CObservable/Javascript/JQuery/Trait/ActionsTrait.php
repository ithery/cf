<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 6:38:10 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CObservable_Javascript_JQuery_Trait_ActionsTrait {

    /**
     * @return string
     */
    abstract function getSelector();

    abstract function resetJQueryStatement();

    abstract function jQueryStatement();

    abstract function filterArgs();

    /**
     * 
     * @param string $class
     */
    public function addClass($class = '') {
        $this->jQueryStatement()->addClass($class);
        $this->resetJQueryStatement();

        return $this;
    }

    public function after($element) {
        $this->jQueryStatement()->after($element);
        $this->resetJQueryStatement();
        return $this;
    }

    public function before($element) {
        $this->jQueryStatement()->before($element);
        $this->resetJQueryStatement();
        return $this;
    }

    public function prepend($element) {
        $this->jQueryStatement()->prepend($element);
        $this->resetJQueryStatement();
        return $this;
    }

    public function append($element) {

        $this->jQueryStatement()->append($element);
        $this->resetJQueryStatement();
        return $this;
    }

    public function val() {
        $args = func_get_args();
        $object = $this->jQueryStatement();
        $object = call_user_func_array(array($object, 'val'), $args);
        $statement = $object;
        $this->resetJQueryStatement();
        if (count($args) == 0) {

            return $statement;
        }
        return $this;
    }

    public function html() {
        $args = func_get_args();
        $object = $this->jQueryStatement();
        $args = $this->filterArgs($args);
        $object = call_user_func_array(array($object, 'html'), $args);

        $statement = $object;
        $this->resetJQueryStatement();
        if (count($args) == 0) {

            return $statement;
        }
        return $this;
    }

    public function find() {
        $args = func_get_args();
        $object = $this->jQueryStatement();
        $object = call_user_func_array(array($object, 'find'), $args);
        if (count($args) > 0) {
            return $this;
        }
        $this->resetJQueryStatement();
        return $this;
    }

    public function remove() {
        $this->jQueryStatement()->remove();
        $this->resetJQueryStatement();
        return $this;
    }

    public function detach() {
        $this->jQueryStatement()->detach();
        $this->resetJQueryStatement();
        return $this;
    }

    public function appendTo(CObservable $to) {
        $this->jQueryStatement()->appendTo($to->jquery()->getSelector());
        $this->resetJQueryStatement();
        return $this;
    }

    public function trigger($eventName) {
        $this->jQueryStatement()->trigger($eventName);
        $this->resetJQueryStatement();
        return $this;
    }

    public function hide() {
        $this->jQueryStatement()->hide();
        $this->resetJQueryStatement();
        return $this;
    }

    public function show() {
        $this->jQueryStatement()->show();
        $this->resetJQueryStatement();
        return $this;
    }
    
    public function toggle() {
        $this->jQueryStatement()->toggle();
        $this->resetJQueryStatement();
        return $this;
    }

}
