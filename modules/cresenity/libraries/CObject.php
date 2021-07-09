<?php

/**
 * @package Cresenity
 */
class CObject {
    use CTrait_Compat_Object,
        CTrait_Macroable;

    protected $id;
    protected $valid_prop = [];
    protected $prop = [];
    protected $domain = '';
    private $friends = [];

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

        throw new CException('Cannot access private property :class::$:key', [':class' => __CLASS__, ':key' => $key]);
    }

    public function __set($key, $value) {
        $trace = debug_backtrace();
        if (isset($trace[1]['class']) && in_array($trace[1]['class'], $this->friends)) {
            return $this->$key = $value;
        }

        throw new CException('Cannot access private property :class::$:key', [':class' => get_called_class(), ':key' => $key]);
    }

    protected function __construct($id = '') {
        $observer = CObserver::instance();
        if ($id == '') {
            $id = spl_object_hash($this);
        }
        $this->id = $id;
        $this->domain = CF::domain();
        $observer->add($this);
    }

    public function regenerateId() {
        $this->id = CObserver::instance()->newId();
    }

    public function id() {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function className() {
        return get_class($this);
    }

    public function domain() {
        return $this->domain;
    }

    public function isUseTrait($trait) {
        $traits = c::classUsesRecursive(get_class($this));
        return isset($traits[$trait]);
    }
}
