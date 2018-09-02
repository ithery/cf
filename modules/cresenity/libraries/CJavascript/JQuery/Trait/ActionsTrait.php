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
     * Execute a javascript library detach action
     *
     * @param string $element element
     * @param string $speed One of 'slow', 'normal', 'fast', or time in milliseconds
     * @param string $callback Javascript callback function
     * @return string
     */
    public function detach($element = 'this') {
        return $this->genericCallValue("detach", $element);
    }
    
    /**
     * Execute a javascript library remove action
     *
     * @param string $element element
     * @param string $speed One of 'slow', 'normal', 'fast', or time in milliseconds
     * @param string $callback Javascript callback function
     * @return string
     */
    public function remove($element = 'this') {
        return $this->genericCallValue("remove", $element);
    }

    /**
     * Insert content, specified by the parameter, after each element in the set of matched elements
     * @param string $to
     * @param string $element
     * @return string
     */
    public function after($to, $element) {
        return $this->genericCallElement('after', $to, $element);
    }

    /**
     * Insert content, specified by the parameter, before each element in the set of matched elements
     * @param string $to
     * @param string $element
     * @return string
     */
    public function before($to, $element) {
        return $this->genericCallElement('before', $to, $element);
    }

    /**
     * Get or set the value of the first element in the set of matched elements or set one or more attributes for every matched element.
     * @param string $element
     * @param string $value
     */
    public function val($element = 'this', $value = null) {
        return $this->genericCallValue('val', $element, $value);
    }

    /**
     * Get or set the html of an attribute for the first element in the set of matched elements.
     * @param string $element
     * @param string $value
     */
    public function html($element = 'this', $value = '') {
        return $this->genericCallValue('html', $element, $value);
    }

    /**
     * Insert content, specified by the parameter $element, to the end of each element in the set of matched elements $to.
     * @param string $to
     * @param string $element
     * @return string
     */
    public function append($to, $element) {
        return $this->genericCallElement('append', $to, $element);
    }
    
    /**
     * Insert content, specified by the parameter $element, to the end of each element in the set of matched elements $to.
     * @param string $to
     * @param string $element
     * @return string
     */
    public function appendTo($element, $to) {
        return $this->genericCallElement('appendTo', $element, $to);
    }

    /**
     * Insert content, specified by the parameter $element, to the beginning of each element in the set of matched elements $to.
     * @param string $to
     * @param string $element
     * @return string
     */
    public function prepend($to, $element) {
        return $this->genericCallElement('prepend', $to, $element);
    }

    /**
     * Execute a javascript library hide action
     *
     * @param string $element element
     * @param string $speed One of 'slow', 'normal', 'fast', or time in milliseconds
     * @param string $callback Javascript callback function
     * @return string
     */
    public function fadeIn($element = 'this', $speed = '', $callback = '') {
        return $this->showHideWithEffect("fadeIn", $element, $speed, $callback);
    }

    /**
     * Execute a javascript library hide action
     *
     * @param string $element element
     * @param string $speed One of 'slow', 'normal', 'fast', or time in milliseconds
     * @param string $callback Javascript callback function
     * @return string
     */
    public function fadeOut($element = 'this', $speed = '', $callback = '') {
        return $this->showHideWithEffect("fadeOut", $element, $speed, $callback);
    }

    /**
     * Execute a javascript library slideUp action
     *
     * @param string $element element
     * @param string $speed One of 'slow', 'normal', 'fast', or time in milliseconds
     * @param string $callback Javascript callback function
     * @return string
     */
    public function slideUp($element = 'this', $speed = '', $callback = '') {
        return $this->showHideWithEffect("slideUp", $element, $speed, $callback);
    }

    /**
     * Execute a javascript library removeClass action
     *
     * @param string $element element
     * @param string $class Class to add
     * @return string
     */
    public function removeClass($element = 'this', $class = '') {
        return $this->genericCallValue('removeClass', $element, $class);
    }

    /**
     * Execute a javascript library slideDown action
     *
     * @param string $element element
     * @param string $speed One of 'slow', 'normal', 'fast', or time in milliseconds
     * @param string $callback Javascript callback function
     * @return string
     */
    public function slideDown($element = 'this', $speed = '', $callback = '') {
        return $this->showHideWithEffect("slideDown", $element, $speed, $callback);
    }

    /**
     * Execute a javascript library slideToggle action
     *
     * @param string $element element
     * @param string $speed One of 'slow', 'normal', 'fast', or time in milliseconds
     * @param string $callback Javascript callback function
     * @return string
     */
    public function slideToggle($element = 'this', $speed = '', $callback = '') {
        return $this->showHideWithEffect("slideToggle", $element, $speed, $callback);
    }

    
    
    /**
     * Execute a javascript library hide action
     *
     * @param string $element element
     * @param string $speed One of 'slow', 'normal', 'fast', or time in milliseconds
     * @param string $callback Javascript callback function
     * @return string
     */
    public function hide($element = 'this', $speed = '', $callback = '') {
        return $this->showHideWithEffect("hide", $element, $speed, $callback);
    }

    /**
     * Execute a javascript library toggle action
     *
     * @param string $element element
     * @param string $speed One of 'slow', 'normal', 'fast', or time in milliseconds
     * @param string $callback Javascript callback function
     * @return string
     */
    public function toggle($element = 'this', $speed = '', $callback = '') {
        return $this->showHideWithEffect("toggle", $element, $speed, $callback);
    }

    /**
     * Execute a javascript library toggle class action
     *
     * @param string $element element
     * @param string $class
     * @return string
     */
    public function toggleClass($element = 'this', $class = '') {
        return $this->genericCallValue('toggleClass', $element, $class);
    }

    /**
     * Execute a javascript library show action
     *
     * @param string $element element
     * @param string $speed One of 'slow', 'normal', 'fast', or time in milliseconds
     * @param string $callback Javascript callback function
     * @return string
     */
    public function show($element = 'this', $speed = '', $callback = '') {
        return $this->showHideWithEffect("show", $element, $speed, $callback);
    }

}
