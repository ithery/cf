<?php
/**
 * @see CFunction
 */
class CFunction_SerializableClosure {
    /**
     * The closure's serializable.
     *
     * @var \CFunction_SerializableClosure_Contract_SerializableInterface
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

        $this->serializable = CFunction_SerializableClosure_Serializer_SignedSerializer::$signer
            ? new CFunction_SerializableClosure_Serializer_SignedSerializer($closure)
            : new CFunction_SerializableClosure_Serializer_NativeSerializer($closure);
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
     * Create a new unsigned serializable closure instance.
     *
     * @param Closure $closure
     *
     * @return \CFunction_UnsignedSerializableClosure
     */
    public static function unsigned(Closure $closure) {
        return new CFunction_UnsignedSerializableClosure($closure);
    }

    /**
     * Sets the serializable closure secret key.
     *
     * @param null|string $secret
     *
     * @return void
     */
    public static function setSecretKey($secret) {
        CFunction_SerializableClosure_Serializer_SignedSerializer::$signer = $secret
            ? new CFunction_SerializableClosure_Signer_HmacSigner($secret)
            : null;
    }

    /**
     * Sets the serializable closure secret key.
     *
     * @param null|\Closure $transformer
     *
     * @return void
     */
    public static function transformUseVariablesUsing($transformer) {
        CFunction_SerializableClosure_Serializer_NativeSerializer::$transformUseVariables = $transformer;
    }

    /**
     * Sets the serializable closure secret key.
     *
     * @param null|\Closure $resolver
     *
     * @return void
     */
    public static function resolveUseVariablesUsing($resolver) {
        CFunction_SerializableClosure_Serializer_NativeSerializer::$resolveUseVariables = $resolver;
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
     * @throws \CFunction_SerializableClosure_Exception_InvalidSignatureException
     *
     * @return void
     */
    public function __unserialize($data) {
        if (CFunction_SerializableClosure_Serializer_SignedSerializer::$signer && !$data['serializable'] instanceof CFunction_SerializableClosure_Serializer_SignedSerializer) {
            throw new CFunction_SerializableClosure_Exception_InvalidSignatureException();
        }

        $this->serializable = $data['serializable'];
    }
}
