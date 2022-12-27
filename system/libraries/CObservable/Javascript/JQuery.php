<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 2, 2018, 5:38:11 PM
 */
class CObservable_Javascript_JQuery {
    use CObservable_Javascript_JQuery_Trait_ActionsTrait,
        CObservable_Javascript_JQuery_Trait_EventsTrait,
        CObservable_Javascript_JQuery_Trait_AjaxTrait,
        CObservable_Javascript_JQuery_Trait_InternalTrait;

    /**
     * @var CObservable_Javascript
     */
    protected $javascript;

    /**
     * @var CJavascript_Statement_JQuery
     */
    protected $jQueryStatement;

    public function __construct($javascript) {
        $this->javascript = $javascript;
    }

    public function getSelector() {
        return $this->javascript->getSelector();
    }

    public function addStatement(CJavascript_Statement $statement) {
        $this->javascript->addStatement($statement);
    }

    public function resetJQueryStatement() {
        if ($this->jQueryStatement != null) {
            $this->addStatement($this->jQueryStatement);
        }
        $this->jQueryStatement = null;
    }

    public function raw($statement) {
        $this->javascript->raw($statement);
    }

    /**
     * @return CJavascript_Statement_JQuery
     */
    public function jQueryStatement() {
        if ($this->jQueryStatement == null) {
            $this->jQueryStatement = CJavascript::jqueryStatement($this->getSelector());
        }

        return $this->jQueryStatement;
    }

    public function filterArgs($args) {
        return $this->javascript->filterArgs($args);
    }
}
