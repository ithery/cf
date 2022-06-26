<?php

class CManager_Transform_Method_ClosureMethod implements CManager_Transform_Contract_TransformMethodInterface {
    public function __construct($closure) {
        $this->closure = new \Opis\Closure\SerializableClosure($closure);
    }

    public function transform($value, array $arguments = []) {
        return $this->closure->invoke($value, ...$arguments);
    }
}
