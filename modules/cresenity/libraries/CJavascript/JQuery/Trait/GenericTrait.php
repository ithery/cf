<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 12:49:09 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CJavascript_JQuery_Trait_GenericTrait {

    abstract public function addEvent($element, $js, $event, $preventDefault = false, $stopPropagation = false);

    /**
     * Execute a generic jQuery call with a value.
     * @param string $jQueryCall
     * @param string $element
     * @param string $param
     */
    public function genericCallValue($jQueryCall, $element = 'this', $param = null) {
        $element = $this->getSelector($element);
        $element = CJavascript_Helper_Javascript::prepElement($element);
        if ($param !== null) {
            $param = CJavascript_Helper_Javascript::prepValue($param);
            $str = "$({$element}).{$jQueryCall}({$param});";
        } else
            $str = "$({$element}).{$jQueryCall}();";

        $this->addScript($str);
        return $str;
    }

    /**
     * Execute a generic jQuery call with 2 elements.
     * @param string $jQueryCall
     * @param string $to
     * @param string $element
     * @return string
     */
    public function genericCallElement($jQueryCall, $to = 'this', $element) {
        $to = $this->getSelector($to);
        $to = CJavascript_Helper_Javascript::prepElement($to);
        $element = CJavascript_Helper_Javascript::prepElement($element);
        $str = "$({$to}).{$jQueryCall}({$element});";

        $this->addScript($str);
        return $str;
    }

    /**
     * Get or set the value of an attribute for the first element in the set of matched elements or set one or more attributes for every matched element.
     * @param string $element
     * @param string $attributeName
     * @param string $value

     */
    public function attr($element = 'this', $attributeName, $value = "") {
        $element = $this->getSelector($element);
        $element = CJavascript_Helper_Javascript::prepElement($element);
        if (isset($value)) {
            $value = CJavascript_Helper_Javascript::prepValue($value);
            $str = "$({$element}).attr(\"$attributeName\",{$value});";
        } else {
            $str = "$({$element}).attr(\"$attributeName\");";
        }
        $this->addScript($str);
        return $str;
    }

    /**
     * Outputs a javascript library animate event
     *
     * @param string $element element
     * @param array|string $params
     * @param string $speed One of 'slow', 'normal', 'fast', or time in milliseconds
     * @param string $extra
     * @return string
     */
    public function animate($element = 'this', $params = array(), $speed = '', $extra = '') {
        $element = $this->getSelector($element);
        $element = CJavascript_Helper_Javascript::prepElement($element);
        $speed = $this->validateSpeed($speed);
        $animations = "\t\t\t";
        if (\is_array($params)) {
            foreach ($params as $param => $value) {
                $animations .= $param . ': \'' . $value . '\', ';
            }
        }
        $animations = substr($animations, 0, -2); // remove the last ", "
        if ($speed != '') {
            $speed = ', ' . $speed;
        }
        if ($extra != '') {
            $extra = ', ' . $extra;
        }
        $str = "$({$element}).animate({\n$animations\n\t\t}" . $speed . $extra . ");";

        $this->addScript($str);
        return $str;
    }

    /**
     * show or hide with effect
     *
     * @param string $action
     * @param string $element element
     * @param string $speed One of 'slow', 'normal', 'fast', or time in milliseconds
     * @param string $callback Javascript callback function
     * @param boolean $immediatly defers the execution if set to false
     * @return string
     */
    protected function showHideWithEffect($action, $element = 'this', $speed = '', $callback = '', $immediatly = false) {
        $element = $this->getSelector($element);
        $element = CJavascript_Helper_Javascript::prepElement($element);
        $speed = $this->validateSpeed($speed);
        if ($callback != '') {
            $callback = ", function(){\n{$callback}\n}";
        }
        $str = "$({$element}).{$action}({$speed}{$callback});";
        $this->addScript($str);
        return $str;
    }

    /**
     * Execute all handlers and behaviors attached to the matched elements for the given event.
     * @param string $element
     * @param string $event
     * @param boolean $immediatly defers the execution if set to false
     */
    public function trigger($element = 'this', $event = 'click', $immediatly = false) {
        $element = $this->getSelector($element);
        $element = CJavascript_Helper_Javascript::prepElement($element);
        $str = "$({$element}).trigger(\"$event\");";

        $this->addScript($str);
        return $str;
    }

    /**
     * Table Sorter Plugin
     *
     * @param string $table table name
     * @param string $options plugin location
     * @return string
     */
    public function tablesorter($table = '', $options = '') {
        $table = $this->getSelector($table);
        $this->addScript("\t$(" . CJavascript_Helper_Javascript::prepElement($table) . ").tablesorter($options);\n");
    }

    /**
     * Call the JQuery method $jqueryCall on $element with parameters $param
     * @param string $element
     * @param string $jqueryCall
     * @param mixed $param
     * @param string $jsCallback javascript code to execute after the jquery call
     * @return string
     */
    private function doJQuery($element, $jqueryCall, $param = "", $jsCallback = "") {
        $element = $this->getSelector($element);
        $param = CJavascript_Helper_Javascript::prepValue($param);
        $callback = "";
        if ($jsCallback != "") {
            $callback = ", function(event){\n{$jsCallback}\n}";
        }
        $script = "$(" . CJavascript_Helper_Javascript::prepElement($element) . ")." . $jqueryCall . "(" . $param . $callback . ");\n";
        $this->addScript($script);
        return $script;
    }

    /**
     *
     * @param string $event
     * @param string $element
     * @param string $elementToModify
     * @param string $jqueryCall
     * @param string|array $param
     * @param boolean $preventDefault
     * @param boolean $stopPropagation
     * @param string $jsCallback javascript code to execute after the jquery call
     * @return string
     */
    private function doJQueryOn($event, $element, $elementToModify, $jqueryCall, $param = "", $preventDefault = false, $stopPropagation = false, $jsCallback = "") {
        return $this->addEvent($element, $this->doJQuery($elementToModify, $jqueryCall, $param, $jsCallback), $event, $preventDefault, $stopPropagation);
    }

    /**
     * Executes the code $js
     * @param string $js Code to execute
     * @return String
     */
    public function exec($js) {
        $script = $js . "\n";
        $this->addScript($script);
        return $script;
    }

    /**
     * Executes the code $js
     * @param string $js Code to execute
     * @param boolean $immediatly delayed if false
     * @return String
     */
    public function execAtLast($js) {
        $script = $js . "\n";
        $this->addScriptLast($script);
        return $script;
    }

    /**
     * Executes the javascript code $js when $event fires on $element
     * @param string $event
     * @param string $element
     * @param string $js Code to execute
     * @param array $parameters default : array("preventDefault"=>false,"stopPropagation"=>false,"immediatly"=>true)
     * @return String
     */
    public function execOn($event, $element, $js, $parameters = array()) {
        $stopPropagation = false;
        $preventDefault = false;
        $immediatly = true;
        extract($parameters);
        $script = $this->addEvent($element, $this->exec($js), $event, $preventDefault, $stopPropagation, $immediatly);
        return $script;
    }

    public function setJsonToElement($json, $elementClass = "_element") {
        $retour = "var data={$json};"
                . "\n\tdata=($.isPlainObject(data))?data:JSON.parse(data);"
                . "\n\tvar pk=data['pk'];var object=data['object'];"
                . "\n\tfor(var field in object){"
                . "\n\tif($('[data-field='+field+']',$('._element[data-ajax='+pk+']')).length){"
                . "\n\t\t$('[data-field='+field+']',$('._element[data-ajax='+pk+']')).each(function(){"
                . "\n\t\t\tif($(this).is('[value]')) { $(this).val(object[field]);} else { $(this).html(object[field]); }"
                . "\n\t});"
                . "\n}};\n";
        $retour .= "\t$(document).trigger('jsonReady',[data]);\n";
        return $this->exec($retour);
    }

    /**
     * Sets an element draggable (HTML5 drag and drop)
     * @param string $element The element selector
     * @param array $parameters default : array("attr"=>"id","preventDefault"=>false,"stopPropagation"=>false,"immediatly"=>true)
     */
    public function setDraggable($element, $parameters = []) {
        $attr = "id";
        extract($parameters);
        $script = $this->addEvent($element, CJavascript_Helper_Javascript::draggable($attr), "dragstart", $parameters);
        return $script;
    }

    /**
     * Declares an element as a drop zone (HTML5 drag and drop)
     * @param string $element The element selector
     * @param array $parameters default : array("attr"=>"id","stopPropagation"=>false,"immediatly"=>true,"jqueryDone"=>"append")
     * @param string $jsCallback the js script to call when element is dropped
     */
    public function asDropZone($element, $jsCallback = "", $parameters = []) {
        $stopPropagation = false;
        $jqueryDone = "append";
        $script = $this->addEvent($element, '', "dragover", true, $stopPropagation);
        extract($parameters);
        $script .= $this->addEvent($element, CJavascript_Helper_Javascript::dropZone($jqueryDone, $jsCallback), "drop", true, $stopPropagation);
        return $script;
    }

    public function interval($jsCode, $time, $globalName = null) {
        if (!CJavascript_Helper_Javascript::isFunction($jsCode)) {
            $jsCode = "function(){\n" . $jsCode . "\n}";
        }
        if (isset($globalName)) {
            $script = "if(window.{$globalName}){clearInterval(window.{$globalName});}\nwindow.{$globalName}=setInterval({$jsCode},{$time});";
        } else {
            $script = "setInterval({$jsCode},{$time});";
        }
        return $this->exec($script);
    }

    public function clearInterval($globalName, $immediatly = true) {
        return $this->exec("if(window.{$globalName}){clearInterval(window.{$globalName});}");
    }

}
