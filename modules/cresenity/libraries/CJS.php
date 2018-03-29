<?php

class CJS {

    private function __construct() {
        
    }

    static public function ready() {
        $statements = func_get_args();
        $js = "JQuery(document).ready(function(){\n";
        foreach ($statements as $stm)
            $js .= CJS::js($stm) . "\n";
        $js .= "});";

        return $js;
    }

    /**
     * Returns a variable name
     * @param string $name The name of the function to create
     * @param string $body A string with the javascript code for the body of the function
     * @param array  $params Optional list of parameter names for the function definition
     * @return CJSStm
     */
    static public function def($name, $body, $params = array()) {
        $name = ($name === null) ? "" : " " . $name;
        if (CJS::is_jsstm($body))
            $body = $body->output();
        $function = "function$name(" . implode(",", $params) . ") {
			$body
		}";

        return CJS::inline($function);
    }

    static public function variable($var) {
        return CJS::inline($var);
    }

    static public function assign($var, $statement) {
        return CJS::stm($var . "=" . $statement);
    }

    static public function assign_new($var, $statement) {
        if (CJS::is_jsstm($statement))
            $stm = $statement->output();
        else
            $stm = $statement;
        return CJS::stm("var " . $var . "=" . $stm);
    }

    /**
     * Creates a javascript statement with a semicolon at the end.
     * @param string Javascript statement code 
     * @return JavascriptStm
     */
    static public function stm($statement) {
        if (CJS::is_jsstm($statement))
            return CJS::inline($statement->output() . ";");
        return CJS::inline($statement . ";");
    }

    /**
     * Creates a javascript statement without a semicolon at the end.
     * @param string Javascript statement code 
     * @return JavascriptStm
     */
    static public function inline($statement) {
        if (CJS::is_jsstm($statement))
            return $statement;
        return new CJSStm($statement);
    }

    /**
     * 
     * @param array Receives indefinite parameters. Each paramenters refers to a string or a <code>JavascriptStm</code>
     * @return string
     */
    static public function output() {
        $statements = func_get_args();
        $evalStms = array();
        foreach ($statements as $stm) {
            if (CJS::is_js($stm) ||
                    CJS::is_jsstm($stm)) {
                $evalStms[] = $stm->output();
            } else
                $evalStms[] = $stm;
        }
        $js = implode("\n", $evalStms);
        return $js;
    }

    static public function js($value, $lazy = false) {
        $resolvedValue = $value;

        if (is_bool($value))
            $resolvedValue = ($value) ? "true" : "false";
        else if (is_null($value))
            $resolvedValue = null;
        else if (is_string($value))
            $resolvedValue = "'$value'";
        else if (is_array($value))
            $resolvedValue = CJS::json_encode($value);
        else if (CObject::is_instanceof($value))
            $resolvedValue = $value->js($lazy);
        else if (CObjectCollection::is_instanceof($value)) {
            if ($value->getCount() > 0)
                $resolvedValue = $value->js($lazy);
        }
        else if (CJS::is_js($value) ||
                CJS::is_jsstm($value))
            $resolvedValue = $value->output();
        else if (is_object($value))
            $resolvedValue = CJS::json_encode($value);

        return $resolvedValue;
    }

    static public function json_encode($value) {
        static $jsonEncoder;
        if (function_exists("json_encode"))
            return json_encode($value);
        else {

            if ($jsonEncoder == null) {
                $DS = DIRECTORY_SEPARATOR;
                include_once('.' . $DS . 'Lib' . $DS . 'json.php');
                $jsonEncoder = new Services_JSON();
            }
            return $jsonEncoder->encode($value);

            return "";
        }
    }

    static public function is_js($value) {
        if (is_object($value)) {
            return ($value instanceof CJS);
        }
        return false;
    }

    static public function is_jsstm($value) {
        if (is_object($value)) {
            return ($value instanceof CJSStm);
        }
        return false;
    }

    static public function send_content_type() {
        header("Content-type:text/javascript");
    }

}

class CJSStm {

    public $statement = "";

    public function __construct($statement) {
        $this->statement = $statement;
    }

    public function output() {
        return $this->statement;
    }

}

?>