<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 2, 2018, 11:07:35 PM
 */
class CObservable_Javascript {
    /**
     * @var string|CRenderable
     */
    protected $owner;

    /**
     * @var CObservable_Javascript_JQuery
     */
    private $jQueryObject;

    /**
     * @var CObservable_Javascript_CresJs
     */
    private $cresObject;

    /**
     * @var CObservable_Javascript_Native
     */
    private $nativeObject;

    /**
     * @var CObservable_Javascript_Handler
     */
    private $handlerObject;

    public function __construct($owner = null) {
        $this->owner = $owner;

        $this->handlerObject = new CObservable_Javascript_Handler($this);
    }

    /**
     * @return $this
     */
    public function startDeferred() {
        CJavascript::pushDeferredStack();

        return $this;
    }

    /**
     * @param CObservable_Javascript|CRenderable $object
     * @param Closure                            $closure
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

        $js = CJavascript::closureToJs($closure);

        $this->startDeferred();
        call_user_func_array($closure, $args);

        return $this->endDeferred();
    }

    /**
     * Get compiled deferred JS.
     *
     * @return CJavascript_Statement[]
     */
    public function endDeferred() {
        $statements = CJavascript::popDeferredStack();

        return $statements;
    }

    /**
     * @param CJavascript_Statement $statement
     *
     * @return $this
     */
    public function addStatement(CJavascript_Statement $statement) {
        CJavascript::addStatement($statement);

        return $this;
    }

    /**
     * @param CJavascript_Statement_Raw $statement
     *
     * @return $this
     */
    public function raw($statement) {
        CJavascript::addRaw($statement);

        return $this;
    }

    /**
     * @param CJavascript_Statement $statement
     *
     * @return $this
     */
    public function removeStatement(CJavascript_Statement $statement) {
        CJavascript::removeDeferredStatement($statement);
        CJavascript::removeStatement($statement);

        return $this;
    }

    /**
     * @return CObservable_Javascript_Native
     */
    public function native() {
        if ($this->nativeObject == null) {
            $this->nativeObject = new CObservable_Javascript_Native($this);
        }

        return $this->nativeObject;
    }

    /**
     * @return CObservable_Javascript_Cresjs
     */
    public function cresjs() {
        if ($this->cresObject == null) {
            $this->cresObject = new CObservable_Javascript_Cresjs($this);
        }

        return $this->cresObject;
    }

    /**
     * @param null|string $selector
     *
     * @return CObservable_Javascript_JQuery
     */
    public function jquery($selector = null) {
        if ($selector != null) {
            return new CObservable_Javascript_JQuery(new CObservable_Javascript($selector));
        }
        if ($this->jQueryObject == null) {
            $this->jQueryObject = new CObservable_Javascript_JQuery($this);
        }

        return $this->jQueryObject;
    }

    /**
     * @return CObservable_Javascript_Handler
     */
    public function handler() {
        return $this->handlerObject;
    }

    public function __call($method, $arguments) {
        if (method_exists($this->nativeObject, $method)) {
            return call_user_func_array([$this->nativeObject, $method], $arguments);
        }

        throw new Exception('Method ' . $method . ' not exists in class ' . self::class);
    }

    /**
     * Get selector of this jquery.
     *
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
     * Get Owner of this jQuery.
     *
     * @return string|CRenderable
     */
    public function getOwner() {
        return $this->owner;
    }

    public function filterArgs($args) {
        if (!is_array($args)) {
            $args = [$args];
        }
        foreach ($args as &$arg) {
            $arg = $this->filterArg($arg);
        }

        return $args;
    }

    public function filterArg($arg) {
        if ($arg instanceof CJavascript_Statement) {
            //this statement will used for args, remove this statement for being rendered
            $this->removeStatement($arg);
        }

        return $arg;
    }

    public function ifStatement($operand1, $operator = null, $operand2 = null) {
        $statement = new CJavascript_Statement_IfStatement($operand1, $operator, $operand2);
    }

    public function createFunction($funcName, $callback) {
        $serializedClosure = new \Opis\Closure\SerializableClosure($callback);
        $serialized = serialize($serializedClosure);
        $regex = "#\:\"(function\s*+\(.+?})\";#ims";
        if (preg_match($regex, $serialized, $matches)) {
            $script = $matches[1];
            CJavascript::phpToJs('<?php' . PHP_EOL . $script . PHP_EOL . '?>');
        }
    }
}
