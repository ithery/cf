<?php

class CManager_Transform_Method_ClosureMethod implements CManager_Transform_Contract_TransformMethodInterface {
    /**
     * @var Closure|CFunction_SerializableClosure
     */
    protected $closure;

    public function __construct($closure) {
        $this->closure = $closure;
        if (!($this->closure instanceof CFunction_SerializableClosure) && !($this->closure instanceof \Opis\Closure\SerializableClosure)) {
            $this->closure = new CFunction_SerializableClosure($closure);
        }
    }

    public function transform($value, $arguments = []) {
        return $this->closure->__invoke($value, $arguments);
    }
}
