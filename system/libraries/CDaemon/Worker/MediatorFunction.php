<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 16, 2019, 5:03:00 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
final class CDaemon_Worker_MediatorFunction extends CDaemon_Worker_MediatorAbstract {

    /**
     * @var callable
     */
    protected $function;

    /**
     * Set a function that will be executed asynchronously in the background. Given the alias "execute()" internally.
     * @param callable $f
     * @throws Exception
     */
    public function setFunction($f) {
        if (!is_callable($f)) {
            throw new Exception(__METHOD__ . " Failed. Supplied argument is not callable!");
        }
        $this->function = $f;
        $this->methods = array('execute');
    }

    protected function getCallback($method) {
        switch ($method) {
            case 'execute':
                return $this->function;
                break;
            case 'setup':
                return function() {
                    
                };
                break;
            case 'teardown':
                $that = $this;
                return function() use ($that) {
                    $that->function = null;
                };
                break;
            default:
                throw new Exception("$method() is Not Callable.");
        }
    }

    public function inline() {
        return call_user_func_array($this->function, func_get_args());
    }

}
