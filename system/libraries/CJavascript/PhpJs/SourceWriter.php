<?php
class CJavascript_PhpJs_SourceWriter implements CJavascript_PhpJs_Contract_SourceWriterInterface {
    public static $indentChr = "\t";

    public static $EOL = PHP_EOL;

    private $code = '';

    private $codeStack = [];

    private $atStartStack = [];

    private $delayStack = [];

    private $finalCode = '';

    private $indent = 0;

    private $indentStr = '';

    private $atStart = true;

    private $lastDelay;

    private $lastKey = 0;

    private function reset() {
        $this->code = '';
        $this->codeStack = [];
        $this->atStartStack = [];
        $this->delayStack = [];
        $this->finalCode = '';

        $this->indent = 0;
        $this->indentStr = '';
        $this->atStart = true;
    }

    /**
     * @param null $atStart
     *
     * @return $this
     */
    public function pushDelay($atStart = null) {
        $this->codeStack[] = $this->code;
        $this->atStartStack[] = $this->atStart;
        $this->code = '';
        if ($atStart !== null) {
            $this->atStart = $atStart;
        }

        return $this;
    }

    /**
     * @param null $id
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function popDelay(&$id = null) {
        $this->delayStack[$id = $this->lastDelay = $this->lastKey++] = $this->code;
        $this->code = array_pop($this->codeStack);
        $this->atStart = array_pop($this->atStartStack);

        return $this;
    }

    /**
     * @param $var
     *
     * @return $this
     */
    public function popDelayToVar(&$var) {
        $var = $this->code;
        $this->code = array_pop($this->codeStack);
        $this->atStart = array_pop($this->atStartStack);

        return $this;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function writeDelay($id) {
        $this->code .= $this->delayStack[$id];
        unset($this->delayStack[$id]);

        return $this;
    }

    /**
     * @return $this
     */
    public function writeLastDelay() {
        return $this->writeDelay($this->lastDelay);
    }

    /**
     * @param $string
     * @param ... $objects
     *
     * @return $this
     */
    public function println($string = '', $objects = null) {
        if ($string && $objects !== null) {
            //$string = call_user_func_array('sprintf',func_get_args());
            $string = $this->printf(func_get_args());
        }
        $this->print($string . self::$EOL);
        $this->atStart = true;

        return $this;
    }

    /**
     * @param $string
     * @param ... $objects
     *
     * @return $this
     */
    public function print($string, $objects = null) {
        if ($string && $objects !== null) {
            //$string = call_user_func_array('sprintf',func_get_args());
            $string = $this->printf(func_get_args());
        }
        if ($this->atStart) {
            $this->code .= $this->indentStr;
            $this->atStart = false;
        }
        $this->code .= $string;

        return $this;
    }

    /**
     * @return $this
     */
    public function indent() {
        $this->indentStr = str_repeat(self::$indentChr, ++$this->indent);

        return $this;
    }

    /**
     * @return $this
     */
    public function outdent() {
        $this->indentStr = str_repeat(self::$indentChr, --$this->indent);

        return $this;
    }

    /**
     * @param $string
     * @param ... $objects
     *
     * @return $this
     */
    public function indentln($string, $objects = null) {
        if ($string && $objects !== null) {
            //$string = call_user_func_array('sprintf',func_get_args());
            $string = $this->printf(func_get_args());
        }
        $this->indent();
        $this->println($string);
        $this->outdent();

        return $this;
    }

    public function getCode() {
        if (count($this->delayStack) > 0) {
            throw new \Exception('DelayStack is not empty (' . count($this->delayStack) . ')');
        }
        if (count($this->codeStack) > 0) {
            throw new \Exception('CodeStack is not empty (' . count($this->codeStack) . ')');
        }

        return $this->finalCode . $this->code;
    }

    public function getResetCode() {
        $res = $this->getCode();
        $this->reset();

        return $res;
    }

    private function printf($args) {
        $string = array_shift($args);
        foreach ($args as $arg) {
            $string = preg_replace('/%{\w*}/', $arg, $string, 1);
        }

        return $string;
    }
}
