<?php
use Opis\Closure\SerializableClosure;

class CManager_Transform_Method_ClosureMethod implements CManager_Transform_Contract_TransformMethodInterface {
    /**
     * @var Closure|\Opis\Closure\SerializableClosure
     */
    protected $closure;

    public function __construct($closure) {
        $this->closure = $closure;
        if (!($this->closure instanceof SerializableClosure)) {
            $this->closure = new SerializableClosure($closure);
        }
    }

    public function transform($value, $arguments = []) {
        return $this->closure->__invoke($value, $arguments);
    }
}
