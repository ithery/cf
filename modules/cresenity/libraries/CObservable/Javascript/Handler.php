<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 20, 2019, 12:11:07 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CObservable_Javascript_Handler {

    /**
     *
     * @var CObservable_Javascript
     */
    protected $javascript;

    public function __construct($javascript) {
        $this->javascript = $javascript;
    }

    public function reload($selector, $options) {
        $variableStatement = CJavascript::rawStatement($js);

        $this->javascript->addStatement($variableStatement);
        return $this;
    }

}
