<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 15, 2019, 12:16:50 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
final class CDaemon_Worker_MediatorObject extends CDaemon_Worker_MediatorAbstract {

    /**
     * @var CDaemon_WorkerAbstract
     */
    protected $object;

    /**
     * The mediated $object's class
     * @var
     */
    protected $class;

    public function __destruct() {
        if (is_object($this->object)) {
            $this->object->teardown();
        }
    }

    public function setObject($o) {
        if (!($o instanceof CDaemon_WorkerAbstract)) {
            throw new Exception(__METHOD__ . " Failed. Worker objects must extends CDaemon_WorkerAbstract");
        }
        $this->object = $o;
        $this->object->mediator = $this;
        $this->class = get_class($o);
        $this->methods = get_class_methods($this->class);
    }

    public function checkEnvironment(array $errors = array()) {
        $errors = array();
        if (!is_object($this->object) || !$this->object instanceof CDaemon_WorkerAbstract) {
            $errors[] = 'Invalid worker object. Workers must extends CDaemon_WorkerAbstract';
        }
        $object_errors = $this->object->check_environment();
        if (is_array($object_errors))
            $errors = array_merge($errors, $object_errors);
        return parent::checkEnvironment($errors);
    }

    protected function getCallback($method) {
        $cb = array($this->object, $method);
        if (is_callable($cb)) {
            return $cb;
        }
        throw new Exception("$method() is Not Callable.");
    }

    /**
     * Return an instance of $object, allowing inline (synchronous) calls that bypass the mediator.
     * Useful if you want to call methods in-process for some reason.
     * Note: Timeouts will not be enforced
     * Note: Your daemon event loop will be blocked until your method calls return.frr
     * @example Your worker object returns data from a webservice, you can put methods in the class to format the data.
     *          In that case you can call it in-process for brevity and convenience.
     * @example $this->DataService->inline()->pretty_print($result);
     * @return Core_IWorker
     */
    public function inline() {
        return $this->object;
    }

}
