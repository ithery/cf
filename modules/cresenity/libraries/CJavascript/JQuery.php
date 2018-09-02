<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 11:48:25 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CJavascript_JQuery {

    protected $scriptForCompile = array();
    protected $scriptForCompileLast = array();
    protected $needForCompile = true;

    use CJavascript_JQuery_Trait_BaseTrait,
        CJavascript_JQuery_Trait_GenericTrait,
        CJavascript_JQuery_Trait_EventsTrait,
        CJavascript_JQuery_Trait_ActionsTrait,
        CJavascript_JQuery_Trait_AjaxTrait,
        CJavascript_JQuery_Trait_InternalTrait;

    /**
     * gather together all script needing to be output
     *
     * @param object $view
     * @param string $view_var view script variable name, default : script_foot
     * @param boolean $script_tags
     * @return string
     */
    public function compile(&$view = NULL, $view_var = 'script_foot', $script_tags = TRUE) {
//        if (isset($this->_ui)) {
//            $this->_compileLibrary($this->_ui, $view);
//        }
//        if (isset($this->_bootstrap)) {
//            $this->_compileLibrary($this->_bootstrap, $view);
//        }
//        if (isset($this->_semantic)) {
//            $this->_compileLibrary($this->_semantic, $view);
//        }
//        if (\sizeof($this->jquery_code_for_compile) == 0) {
//            return;
//        }
//        $this->jquery_code_for_compile = \array_merge($this->jquery_code_for_compile, $this->jquery_code_for_compile_at_last);
//        // Inline references
//        $script = $this->ready(implode('', $this->jquery_code_for_compile));
//        if ($this->params["defer"]) {
//            $script = $this->defer($script);
//        }
//        $script .= ";";
//        $this->jquery_code_for_compile = array();
//        if ($this->params["debug"] === false) {
//            $script = $this->minify($script);
//        }
//        $output = ($script_tags === FALSE) ? $script : $this->inline($script);
//        if ($view !== NULL) {
//            $this->createScriptVariable($view, $view_var, $output);
//        }
        $output = implode('', array_merge($this->scriptForCompile, $this->scriptForCompileLast));
        return $output;
    }

    /**
     * Constructs the syntax for an event, and adds to into the array for compilation
     *
     * @param string $element The element to attach the event to
     * @param string $js The code to execute
     * @param string $event The event to pass
     * @param boolean $preventDefault If set to true, the default action of the event will not be triggered.
     * @param boolean $stopPropagation Prevents the event from bubbling up the DOM tree, preventing any parent handlers from being notified of the event.
     * @return string
     */
    public function addEvent($element, $js, $event, $preventDefault = false, $stopPropagation = false) {
        $element = $this->getSelector($element);
        if (\is_array($js)) {
            $js = implode("\n\t\t", $js);
        }
        if ($preventDefault === true) {
            $js = CJavascript_Helper_Javascript::$preventDefault . $js;
        }
        if ($stopPropagation === true) {
            $js = CJavascript_Helper_Javascript::$stopPropagation . $js;
        }
        if (array_search($event, $this->jqueryEvents) === false) {
            $event = "\n\t$(" . CJavascript_Helper_Javascript::prepElement($element) . ").bind('{$event}',function(event){\n\t\t{$js}\n\t});\n";
        } else {
            $event = "\n\t$(" . CJavascript_Helper_Javascript::prepElement($element) . ").{$event}(function(event){\n\t\t{$js}\n\t});\n";
        }
        $this->addScript($event);
        return $event;
    }

    public function addScript($code) {
        if ($this->needForCompile) {
            $this->scriptForCompile[] = $code;
        }
    }

    public function addScriptLast($code) {
        if ($this->needForCompile) {
            $this->scriptForCompileLast[] = $code;
        }
    }

    public function setNeedForCompile($bool = true) {
        $this->needForCompile = $bool;
    }

    public function getUrl($url) {
        return url($url);
    }

}
