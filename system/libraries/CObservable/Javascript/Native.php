<?php

defined('SYSPATH') or die('No direct access allowed.');

class CObservable_Javascript_Native {
    /**
     * @var CObservable_Javascript
     */
    protected $javascript;

    public function __construct($javascript) {
        $this->javascript = $javascript;
    }

    public function variable($varName, $varValue) {
        $variableStatement = CJavascript::variableStatement($varName, $varValue);

        $this->javascript->addStatement($variableStatement);

        return $this;
    }

    public function raw($js) {
        $variableStatement = CJavascript::createRawStatement($js);

        $this->javascript->addStatement($variableStatement);

        return $this;
    }
}
