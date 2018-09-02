<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 5:38:11 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CRenderable_Observable_JQuery {

    use CRenderable_Observable_JQuery_Trait_ActionsTrait,
        CRenderable_Observable_JQuery_Trait_EventsTrait;

    /**
     *
     * @var CJavacript_JQuery
     */
    protected $jquery;

    /**
     *
     * @var CJavacript_JQuery
     */
    protected $jqueryDeferred;

    /**
     *
     * @var string|CRenderable
     */
    protected $owner;

    /**
     *
     * @var bool
     */
    protected $deferred;

    public function __construct($owner = null) {
        $this->owner = $owner;
        $this->jqueryDeferred = CJavascript::createJQueryDeferred();
        $this->jquery = CJavascript::createJQuery();
    }

    public function getScript() {
        
    }

    public function setOwner($owner) {
        $this->owner = $owner;
        return $this->owner;
    }

    /**
     * Get Owner of this jQuery
     * @return string|CRenderable
     */
    public function getOwner() {
        return $this->owner;
    }

    /**
     * 
     * @return $this
     */
    public function startDeferred() {
        $this->deferred = true;
        $this->jqueryDeferred->clearScript();
        return $this;
    }

    /**
     * 
     * @param CRenderable_Observable_JQuery|CRenderable $renderable
     * @param Closure $closure
     */
    public function bindDeferred($object, Closure $closure) {
        $bindJQuery = $object;
        if($bindJQuery instanceof CRenderable) {
            $bindJQuery=$bindJQuery->jquery();
        }
       
        $bindJQuery->startDeferred();

        $closure($bindJQuery);
        $compiledJs = $bindJQuery->endDeferred();
        $this->getObject()->addScript($compiledJs);
    }

    /**
     * get Compiled deferred JS
     * @return string
     */
    public function endDeferred() {
        $this->deferred = false;
        $script = $this->jqueryDeferred->compile();
        return $script;
    }

    /**
     * 
     * @return CJavascript_JQuery
     */
    public function getObject() {
        if ($this->deferred) {
            return $this->jqueryDeferred;
        }
        return $this->jquery;
    }

    /**
     * Get selector of this jquery
     * @return string
     */
    public function getSelector() {
        $selector = $this->owner;
        if ($selector instanceof CRenderable) {
            $selector = '#' . $selector->id();
        }
        if ($selector == null) {
            $selector = 'this';
        }
        return $selector;
    }

}
