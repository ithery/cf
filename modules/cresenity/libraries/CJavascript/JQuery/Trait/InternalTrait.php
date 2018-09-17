<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 1:56:59 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CJavascript_JQuery_Trait_InternalTrait {

    protected function defer($script) {
        $result = "window.defer=function (method) {if (window.jQuery) method(); else setTimeout(function() { defer(method) }, 50);};";
        $result .= "window.defer(function(){" . $script . "})";
        return $result;
    }

    protected function ready($script) {
        $result = '$(document).ready(function() {' . "\n";
        $result .= $script . '})';
        return $result;
    }

    protected function minify($input) {
        if (trim($input) === "")
            return $input;
        $input = preg_replace(
                array(
            // Remove comment(s)
            '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
            // Remove white-space(s) outside the string and regex
            '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
            // Remove the last semicolon
            //'#;+\}#',
            // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
            '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
            // --ibid. From `foo['bar']` to `foo.bar`
            '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
                ), array(
            '$1',
            '$1$2',
            //'}',
            '$1$3',
            '$1.$3'
                ), $input);
        $input = str_replace("}$", "};$", $input);
        return $input;
    }

    /**
     * Outputs an opening <script>
     *
     * @param string $src
     * @return string
     */
    protected function openScript($src = '') {
        $str = '<script type="text/javascript" ';
        $str .= ($src == '') ? '>' : ' src="' . $src . '">';
        return $str;
    }

    /**
     * Outputs an closing </script>
     *
     * @param string $extra
     * @return string
     */
    protected function closeScript($extra = "\n") {
        return "</script>$extra";
    }

}
