<?php

defined('SYSPATH') or die('No direct access allowed.');

class CJavascript_Statement_JQuery_Event implements CJavascript_Statement_JQuery_CompilableInterface {
    protected $jqueryEvents = [
        'bind', 'blur', 'change', 'click', 'dblclick', 'delegate', 'die', 'error', 'focus', 'focusin', 'focusout', 'hover', 'keydown', 'keypress', 'keyup', 'live', 'load', 'mousedown', 'mouseenter', 'mouseleave', 'mousemove', 'mouseout', 'mouseover', 'mouseup', 'off', 'on', 'one', 'ready', 'resize', 'scroll', 'select', 'submit', 'toggle', 'trigger', 'triggerHandler', 'undind', 'undelegate', 'unload'
    ];

    protected $name;

    protected $js;

    protected $preventDefault;

    protected $stopPropagation;

    protected $retFalse;

    public function __construct($eventName, $js, $options) {
        $retFalse = carr::get($options, 'retFalse', true);
        $preventDefault = carr::get($options, 'preventDefault', false);
        $stopPropagation = carr::get($options, 'stopPropagation', false);
        $this->name = $eventName;

        $this->js = $js;
        $this->preventDefault = $preventDefault;
        $this->stopPropagation = $stopPropagation;
        $this->retFalse = $retFalse;
    }

    public function getName() {
        return $this->name;
    }

    public function compile() {
        $statements = $this->js;

        if (!is_array($statements)) {
            $statements = [$statements];
        }
        $js = '';
        foreach ($statements as $statement) {
            if ($statement instanceof CJavascript_Statement) {
                $statement = $statement->getStatement();
            }
            $js .= $statement;
        }

        if (\is_array($js)) {
            $js = implode("\n\t\t", $js);
        }
        if ($this->preventDefault === true) {
            $js = CJavascript_Helper_Javascript::$preventDefault . $js;
        }
        if ($this->stopPropagation === true) {
            $js = CJavascript_Helper_Javascript::$stopPropagation . $js;
        }

        if (array_search($this->name, $this->jqueryEvents) === false) {
            $event = ".bind('{$this->name}',function(event){\n\t\t{$js}\n\t});\n";
        } else {
            $event = ".{$this->name}(function(event){\n\t\t{$js}\n\t});\n";
        }

        return $event;
    }
}
