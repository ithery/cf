<?php

class CManager_Transform_Method_ClosureMethod implements CManager_Transform_Contract_TransformMethodInterface {
    public function __construct($closure) {
        $this->closure = $closure;
        if (!($this->closure instanceof \Opis\Closure\SerializableClosure)) {
            $this->closure = new \Opis\Closure\SerializableClosure($closure);
        }
    }

    public function transform($value, $arguments = []) {
        return $this->closure->__invoke($value, $arguments);
    }
}
