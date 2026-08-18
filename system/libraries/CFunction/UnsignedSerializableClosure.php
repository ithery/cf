<?php

class CFunction_UnsignedSerializableClosure {
    /**
     * The closure's serializable.
     *
     * @var \CFunction_SerializableClosure_Contract_Serializable
     */
    protected $serializable;

    /**
     * Creates a new serializable closure instance.
     *
     * @param \Closure $closure
     *
     * @return void
     */
    public function __construct(Closure $closure) {
        if (\PHP_VERSION_ID < 70400) {
            throw new CFunction_SerializableClosure_Exception_PhpVersionNotSupportedException();
        }

        $this->serializable = new CFunction_SerializableClosure_Serializer_NativeSerializer($closure);
    }

    /**
     * Resolve the closure with the given arguments.
     *
     * @return mixed
     */
    public function __invoke() {
        if (\PHP_VERSION_ID < 70400) {
            throw new CFunction_SerializableClosure_Exception_PhpVersionNotSupportedException();
        }

        return call_user_func_array($this->serializable, func_get_args());
    }

    /**
     * Gets the closure.
     *
     * @return \Closure
     */
    public function getClosure() {
        if (\PHP_VERSION_ID < 70400) {
            throw new CFunction_SerializableClosure_Exception_PhpVersionNotSupportedException();
        }

        return $this->serializable->getClosure();
    }

    /**
     * Get the serializable representation of the closure.
     *
     * @return array
     */
    public function __serialize() {
        return [
            'serializable' => $this->serializable,
        ];
    }

    /**
     * Restore the closure after serialization.
     *
     * @param array $data
     *
     * @return void
     */
    public function __unserialize($data) {
        $this->serializable = $data['serializable'];
    }
}
