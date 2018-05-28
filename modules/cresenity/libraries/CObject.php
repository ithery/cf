<?php

/**
 * @package Cresenity
 */
class CObject {

    use CTrait_Compat_Object;
    
    protected $id;
    protected $valid_prop = array();
    protected $prop = array();
    protected $domain = "";
    private $friends = array();

    public function addFriend($classname) {
        $this->friends[] = $classname;
    }

    public function setDomain($domain) {
        $this->domain = $domain;
    }

    public function __get($key) {
        $trace = debug_backtrace();

        if (isset($trace[1]['class']) && in_array($trace[1]['class'], $this->friends)) {
            return $this->$key;
        }

        // normal __get() code here
        throw new CException('Cannot access private property :class::$:key', array(':class' => __CLASS__, ':key' => $key));
        //trigger_error(, E_USER_ERROR);
    }

    public function __set($key, $value) {
        $trace = debug_backtrace();
        if (isset($trace[1]['class']) && in_array($trace[1]['class'], $this->friends)) {
            return $this->$key = $value;
        }

        // normal __set() code here

        trigger_error('Cannot access private property ' . __CLASS__ . '::$' . $key, E_USER_ERROR);
    }

    protected function __construct($id = "") {
        $observer = CObserver::instance();
        if ($id == "") {
            $id = spl_object_hash($this);
        }
        $this->id = $id;
        $this->domain = CF::domain();
        $observer->add($this);
    }

    public function regenerateId() {
        $this->id = CObserver::instance()->new_id();
    }

    public function id() {
        return $this->id;
    }

    public function className() {
        return get_class($this);
    }

    public function domain() {
        return $this->domain;
    }

    static public function isInstanceof($value) {
        if (is_object($value)) {
            return ($value instanceof CObject);
        }
        return false;
    }

}
