<?php

class CFunction_SerializableClosure_Serializer_SignedSerializer implements CFunction_SerializableClosure_Contract_SerializableInterface {
    /**
     * The signer that will sign and verify the closure's signature.
     *
     * @var null|\CFunction_SerializableClosure_Contract_SignerInterface
     */
    public static $signer;

    /**
     * The closure to be serialized/unserialized.
     *
     * @var \Closure
     */
    protected $closure;

    /**
     * Creates a new serializable closure instance.
     *
     * @param \Closure $closure
     *
     * @return void
     */
    public function __construct($closure) {
        $this->closure = $closure;
    }

    /**
     * Resolve the closure with the given arguments.
     *
     * @return mixed
     */
    public function __invoke() {
        return call_user_func_array($this->closure, func_get_args());
    }

    /**
     * Gets the closure.
     *
     * @return \Closure
     */
    public function getClosure() {
        return $this->closure;
    }

    /**
     * Get the serializable representation of the closure.
     *
     * @return array
     */
    public function __serialize() {
        if (!static::$signer) {
            throw new CFunction_SerializableClosure_Exception_MissingSecretKeyException();
        }

        return static::$signer->sign(
            serialize(new CFunction_SerializableClosure_Serializer_NativeSerializer($this->closure))
        );
    }

    /**
     * Restore the closure after serialization.
     *
     * @param array $signature
     *
     * @throws \CFunction_SerializableClosure_Exception_InvalidSignatureException
     *
     * @return void
     */
    public function __unserialize($signature) {
        if (static::$signer && !static::$signer->verify($signature)) {
            throw new CFunction_SerializableClosure_Exception_InvalidSignatureException();
        }

        /** @var \Laravel\SerializableClosure\Contracts\Serializable $serializable */
        $serializable = unserialize($signature['serializable']);

        $this->closure = $serializable->getClosure();
    }
}
