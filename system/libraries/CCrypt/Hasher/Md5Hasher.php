<?php

class CCrypt_Hasher_Md5Hasher extends CCrypt_HasherAbstract implements CCrypt_HasherInterface {
    /**
     * Hash the given value.
     *
     * @param string $value
     * @param array  $options
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function make($value, array $options = []) {
        $hash = md5($value);
        if ($hash === false) {
            throw new RuntimeException('Md5 hashing not supported.');
        }

        return $hash;
    }

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param string $hashedValue
     * @param array  $options
     *
     * @return bool
     */
    public function needsRehash($hashedValue, array $options = []) {
        return false;
    }

    /**
     * Check the given plain value against a hash.
     *
     * @param string $value
     * @param string $hashedValue
     * @param array  $options
     *
     * @return bool
     */
    public function check($value, $hashedValue, array $options = []) {
        if (strlen($hashedValue) === 0) {
            return false;
        }

        return md5($value) == $hashedValue;
    }
}
