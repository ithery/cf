<?php

class CCrypt_Hasher_BcryptHasher extends CCrypt_HasherAbstract implements CCrypt_HasherInterface {
    /**
     * The default cost factor.
     *
     * @var int
     */
    protected $rounds = 10;

    /**
     * Indicates whether to perform an algorithm check.
     *
     * @var bool
     */
    protected $verifyAlgorithm = false;

    /**
     * Create a new hasher instance.
     *
     * @param array $options
     *
     * @return void
     */
    public function __construct(array $options = []) {
        $this->rounds = carr::get($options, 'rounds', $this->rounds);
        $this->verifyAlgorithm = carr::get($options, 'verify', $this->verifyAlgorithm);
    }

    /**
     * Hash the given value.
     *
     * @param string $value
     * @param array  $options
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function make($value, array $options = []) {
        $hash = password_hash($value, PASSWORD_BCRYPT, [
            'cost' => $this->cost($options),
        ]);

        if ($hash === false) {
            throw new RuntimeException('Bcrypt hashing not supported.');
        }

        return $hash;
    }

    /**
     * Check the given plain value against a hash.
     *
     * @param string $value
     * @param string $hashedValue
     * @param array  $options
     *
     * @throws \RuntimeException
     *
     * @return bool
     */
    public function check($value, $hashedValue, array $options = []) {
        if ($this->verifyAlgorithm && !$this->isUsingCorrectAlgorithm($hashedValue)) {
            throw new RuntimeException('This password does not use the Bcrypt algorithm.');
        }

        return parent::check($value, $hashedValue, $options);
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
        return password_needs_rehash($hashedValue, PASSWORD_BCRYPT, [
            'cost' => $this->cost($options),
        ]);
    }

    /**
     * Verifies that the configuration is less than or equal to what is configured.
     *
     * @internal
     *
     * @param mixed $value
     */
    public function verifyConfiguration($value) {
        return $this->isUsingCorrectAlgorithm($value) && $this->isUsingValidOptions($value);
    }

    /**
     * Verify the hashed value's algorithm.
     *
     * @param string $hashedValue
     *
     * @return bool
     */
    protected function isUsingCorrectAlgorithm($hashedValue) {
        return $this->info($hashedValue)['algoName'] === 'bcrypt';
    }

    /**
     * Verify the hashed value's options.
     *
     * @param string $hashedValue
     *
     * @return bool
     */
    protected function isUsingValidOptions($hashedValue) {
        list('options' => $options) = $this->info($hashedValue);

        if (!is_int($options['cost'] ?? null)) {
            return false;
        }

        if ($options['cost'] > $this->rounds) {
            return false;
        }

        return true;
    }

    /**
     * Set the default password work factor.
     *
     * @param int $rounds
     *
     * @return $this
     */
    public function setRounds($rounds) {
        $this->rounds = (int) $rounds;

        return $this;
    }

    /**
     * Extract the cost value from the options array.
     *
     * @param array $options
     *
     * @return int
     */
    protected function cost(array $options = []) {
        return carr::get($options, 'rounds', $this->rounds);
    }
}
