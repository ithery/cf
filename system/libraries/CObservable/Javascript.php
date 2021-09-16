<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 11:07:35 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CObservable_Javascript {

    /**
     *
     * @var string|CRenderable
     */
    protected $owner;

    /**
     *
     * @var CObservable_Javascript_JQuery
     */
    private $jQueryObject;

    /**
     *
     * @var CObservable_Javascript_Native
     */
    private $nativeObject;

    /**
     *
     * @var CObservable_Javascript_Handler
     */
    private $handlerObject;

    public function __construct($owner = null) {
        $this->owner = $owner;
        $this->jQueryObject = new CObservable_Javascript_JQuery($this);
        $this->nativeObject = new CObservable_Javascript_Native($this);
        $this->handlerObject = new CObservable_Javascript_Handler($this);
    }

    /**
     * 
     * @return $this
     */
    public function startDeferred() {

        CJavascript::pushDeferredStack();
        return $this;
    }

    /**
     * 
     * @param CObservable_Javascript|CRenderable $renderable
     * @param Closure $closure
     */
    public function bindDeferred($object, Closure $closure) {
        $bindJs = $object;
        if ($bindJs instanceof CRenderable) {
            $bindJs = $bindJs->javascript();
        }

        $bindJs->startDeferred();

        $closure($bindJs);
        $statements = $bindJs->endDeferred();

        foreach ($statements as $s) {
            $this->addStatement($s);
        }
    }

    public function runClosure() {

        $args = func_get_args();
        $closure = carr::get($args, 0);
        $args = array_slice($args, 1);

        $this->startDeferred();
        call_user_func_array($closure, $args);

        return $this->endDeferred();
    }

    /**
     * get compiled deferred JS
     * @return CJavascript_Statement[]
     */
    public function endDeferred() {

        $statements = CJavascript::popDeferredStack();
        return $statements;
    }

    /**
     * 
     * @param CJavascript_Statement $statement
     * @return $this
     */
    public function addStatement(CJavascript_Statement $statement) {


        CJavascript::addStatement($statement);

        return $this;
    }

    /**
     * 
     * @param CJavascript_Statement $statement
     * @return $this
     */
    public function removeStatement(CJavascript_Statement $statement) {

        CJavascript::removeDeferredStatement($statement);
        CJavascript::removeStatement($statement);
        return $this;
    }

    /**
     * 
     * @return CObservable_Javascript_Native
     */
    public function native() {
        return $this->nativeObject;
    }

    /**
     * 
     * @return CObservable_Javascript_JQuery
     */
    public function jquery() {
        return $this->jQueryObject;
    }
    
    
    /**
     * 
     * @return CObservable_Javascript_Handler
     */
    public function handler() {
        return $this->handlerObject;
    }

    public function __call($method, $arguments) {
        if (method_exists($this->nativeObject, $method)) {
            return call_user_func_array(array($this->nativeObject, $method), $arguments);
        }
        throw new Exception('Method ' . $method . ' not exists in class ' . self::class);
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

    public function filterArgs($args) {
        if (!is_array($args)) {
            $args = array($args);
        }
        foreach ($args as &$arg) {
            $arg = $this->filterArg($arg);
        }
        return $args;
    }

    public function filterArg($arg) {
        if ($arg instanceOf CJavascript_Statement) {
            //this statement will used for args, remove this statement for being rendered
            $this->removeStatement($arg);
        }
        return $arg;
    }

}
