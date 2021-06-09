<?php

/**
 * Description of EncrypterInterface
 *
 * @author Hery
 */
interface CCrypt_EncrypterInterface {

    /**
     * Encrypt the given value.
     *
     * @param  mixed  $value
     * @param  bool  $serialize
     * @return string
     *
     * @throws \CCrypt_Exception_EncryptException
     */
    public function encrypt($value, $serialize = true);

    /**
     * Decrypt the given value.
     *
     * @param  string  $payload
     * @param  bool  $unserialize
     * @return mixed
     *
     * @throws \CCrypt_Exception_DecryptException
     */
    public function decrypt($payload, $unserialize = true);
}
