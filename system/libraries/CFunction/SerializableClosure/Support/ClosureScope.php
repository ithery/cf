<?php

use SplObjectStorage;

class CFunction_SerializableClosure_Support_ClosureScope extends SplObjectStorage {
    /**
     * The number of serializations in current scope.
     *
     * @var int
     */
    public $serializations = 0;

    /**
     * The number of closures that have to be serialized.
     *
     * @var int
     */
    public $toSerialize = 0;
}
