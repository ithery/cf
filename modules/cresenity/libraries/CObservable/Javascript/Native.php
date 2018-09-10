<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 3, 2018, 1:15:13 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CObservable_Javascript_Native {

    /**
     *
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
        $variableStatement = CJavascript::rawStatement($js);

        $this->javascript->addStatement($variableStatement);
        return $this;
    }

}
