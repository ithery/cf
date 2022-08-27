<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @see CJavascript_Statement_JQuery
 * @since Sep 2, 2018, 6:38:10 PM
 */
trait CObservable_Javascript_JQuery_Trait_ActionsTrait {
    /**
     * @param string $class
     */
    public function addClass($class = '') {
        /** @var CObservable_Javascript_JQuery $this */
        $this->jQueryStatement()->addClass($class);
        $this->resetJQueryStatement();

        return $this;
    }

    /**
     * @param string $class
     */
    public function toggleClass($class = '') {
        /** @var CObservable_Javascript_JQuery $this */
        $this->jQueryStatement()->toggleClass($class);
        $this->resetJQueryStatement();

        return $this;
    }

    /**
     * @param string $class
     */
    public function removeClass($class = '') {
        /** @var CObservable_Javascript_JQuery $this */
        $this->jQueryStatement()->removeClass($class);
        $this->resetJQueryStatement();

        return $this;
    }

    /**
     * @param string $class
     */
    public function hasClass($class = '') {
        /** @var CObservable_Javascript_JQuery $this */
        $this->jQueryStatement()->hasClass($class);
        $this->resetJQueryStatement();

        return $this;
    }

    public function after($element) {
        /** @var CObservable_Javascript_JQuery $this */
        $this->jQueryStatement()->after($element);
        $this->resetJQueryStatement();

        return $this;
    }

    public function before($element) {
        /** @var CObservable_Javascript_JQuery $this */
        $this->jQueryStatement()->before($element);
        $this->resetJQueryStatement();

        return $this;
    }

    public function prepend($element) {
        /** @var CObservable_Javascript_JQuery $this */
        $this->jQueryStatement()->prepend($element);
        $this->resetJQueryStatement();

        return $this;
    }

    public function append($element) {
        /** @var CObservable_Javascript_JQuery $this */
        $this->jQueryStatement()->append($element);
        $this->resetJQueryStatement();

        return $this;
    }

    public function val() {
        /** @var CObservable_Javascript_JQuery $this */
        $args = func_get_args();
        $object = $this->jQueryStatement();
        $object = call_user_func_array([$object, 'val'], $args);
        $statement = $object;
        $this->resetJQueryStatement();
        if (count($args) == 0) {
            return $statement;
        }

        return $this;
    }

    public function html() {
        /** @var CObservable_Javascript_JQuery $this */
        $args = func_get_args();
        $object = $this->jQueryStatement();
        $args = $this->filterArgs($args);
        $object = call_user_func_array([$object, 'html'], $args);

        $statement = $object;
        $this->resetJQueryStatement();
        if (count($args) == 0) {
            return $statement;
        }

        return $this;
    }

    public function find() {
        /** @var CObservable_Javascript_JQuery $this */
        $args = func_get_args();
        $object = $this->jQueryStatement();
        $object = call_user_func_array([$object, 'find'], $args);
        if (count($args) > 0) {
            return $this;
        }
        $this->resetJQueryStatement();

        return $this;
    }

    public function closest() {
        /** @var CObservable_Javascript_JQuery $this */
        $args = func_get_args();
        $object = $this->jQueryStatement();
        $object = call_user_func_array([$object, 'closest'], $args);
        if (count($args) > 0) {
            return $this;
        }
        $this->resetJQueryStatement();

        return $this;
    }

    public function remove() {
        /** @var CObservable_Javascript_JQuery $this */
        $this->jQueryStatement()->remove();
        $this->resetJQueryStatement();

        return $this;
    }

    public function detach() {
        /** @var CObservable_Javascript_JQuery $this */
        $this->jQueryStatement()->detach();
        $this->resetJQueryStatement();

        return $this;
    }

    public function appendTo(CObservable $to) {
        /** @var CObservable_Javascript_JQuery $this */
        $this->jQueryStatement()->appendTo($to->jquery()->getSelector());
        $this->resetJQueryStatement();

        return $this;
    }

    public function trigger($eventName) {
        /** @var CObservable_Javascript_JQuery $this */
        $this->jQueryStatement()->trigger($eventName);
        $this->resetJQueryStatement();

        return $this;
    }

    public function hide() {
        /** @var CObservable_Javascript_JQuery $this */
        $this->jQueryStatement()->hide();
        $this->resetJQueryStatement();

        return $this;
    }

    public function show() {
        /** @var CObservable_Javascript_JQuery $this */
        $this->jQueryStatement()->show();
        $this->resetJQueryStatement();

        return $this;
    }

    public function slideToggle() {
        /** @var CObservable_Javascript_JQuery $this */
        $this->jQueryStatement()->slideToggle();
        $this->resetJQueryStatement();

        return $this;
    }

    public function toggle() {
        /** @var CObservable_Javascript_JQuery $this */
        $this->jQueryStatement()->toggle();
        $this->resetJQueryStatement();

        return $this;
    }
}
