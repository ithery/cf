<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 3, 2018, 1:15:13 AM
 */
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
        $variableStatement = CJavascript::rawStatement($js);

        $this->javascript->addStatement($variableStatement);

        return $this;
    }
}
