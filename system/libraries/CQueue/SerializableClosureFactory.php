<?php
use Opis\Closure\SerializableClosure as OpisSerializableClosure;

class CQueue_SerializableClosureFactory {
    /**
     * Creates a new serializable closure from the given closure.
     *
     * @param \Closure $closure
     *
     * @return \OpisSerializableClosure
     */
    public static function make($closure) {
        if (\PHP_VERSION_ID < 70400) {
            return new OpisSerializableClosure($closure);
        }

        return new CFunction_SerializableClosure($closure);
    }
}
