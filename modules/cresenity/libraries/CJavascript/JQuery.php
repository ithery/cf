<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 11:48:25 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CJavascript_JQuery {

    protected $codeForCompile = array();

    use CJavascript_JQuery_Trait_BaseTrait,
        CJavascript_JQuery_Trait_GenericTrait,
        CJavascript_JQuery_Trait_ActionsTrait;

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
        $output = implode('', $this->codeForCompile);
        return $output;
    }

}
