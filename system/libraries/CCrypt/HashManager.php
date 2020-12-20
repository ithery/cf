<?php

class CCrypt_HashManager extends CBase_ManagerAbstract implements CCrypt_HasherInterface {
    /**
     * Current singleton instance
     *
     * @var CCrypt_HashManager
     */
    private static $instance;

    /**
     * Get current instances
     *
     * @return CCrypt_HashManager
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Create an instance of the Bcrypt hash Driver.
     *
     * @return CCrypt_Hasher_BcryptHasher
     */
    public function createBcryptDriver() {
        return new CCrypt_Hasher_BcryptHasher($this->config->get('hashing.bcrypt') ?? []);
    }

    /**
     * Create an instance of the Argon2i hash Driver.
     *
     * @return CCrypt_Hasher_ArgonHasher
     */
    public function createArgonDriver() {
        return new CCrypt_Hasher_ArgonHasher($this->config->get('hashing.argon') ?? []);
    }

    /**
     * Create an instance of the Argon2id hash Driver.
     *
     * @return CCrypt_Hasher_Argon2IdHasher
     */
    public function createArgon2idDriver() {
        return new CCrypt_Hasher_Argon2IdHasher($this->config->get('hashing.argon') ?? []);
    }

    /**
     * Get information about the given hashed value.
     *
     * @param string $hashedValue
     *
     * @return array
     */
    public function info($hashedValue) {
        return $this->driver()->info($hashedValue);
    }

    /**
     * Hash the given value.
     *
     * @param string $value
     * @param array  $options
     *
     * @return string
     */
    public function make($value, array $options = []) {
        return $this->driver()->make($value, $options);
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
        return $this->driver()->check($value, $hashedValue, $options);
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
        return $this->driver()->needsRehash($hashedValue, $options);
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver() {
        return $this->config->get('hashing.driver', 'bcrypt');
    }
}
