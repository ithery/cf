<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 8:32:13 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CJavascript_Statement_JQuery_Method implements CJavascript_Statement_JQuery_CompilableInterface {

    protected $name;

    /**
     *
     * @var array
     */
    protected $parameters;

    public function __construct($name, $parameters = array()) {
        $this->name = $name;
        if (!is_array($parameters)) {
            $parameters = array($parameters);
        }
        $this->parameters = $parameters;
    }

    public function getName() {
        return $this->name;
    }

    public function getParameters() {
        return $this->parameters;
    }

    public function compile() {
        $str = '';
        $jQueryCall = $this->getName();
        $params = $this->getParameters();
        $paramPrepared = '';
        if (is_array($params) && count($params) > 0) {
            $preps = array();
            foreach ($params as $param) {
                $needQuoted = true;

                if ($param instanceof CJavascript_Mock_Variable) {
                    $param = $param->getScript();
                    $needQuoted = false;
                }
                if ($param instanceof CJavascript_Statement) {
                    $param = trim($param->getStatement());
                    $param = trim($param, ';');
                    $needQuoted = false;
                }
                if (\is_array($param)) {
                    $param = implode(",", $param);
                    $needQuoted = false;
                }
                if ($needQuoted) {
                    $param = \str_replace(["\\", "\""], ["\\\\", "\\\""], $param);
                    $param = '"' . $param . '"';
                }
                $param = trim($param, "%");
                $preps[] = $param;
            }
            $paramPrepared = implode(",", $preps);
        }
        $str .= ".{$jQueryCall}({$paramPrepared})";
        return $str;
    }

}
