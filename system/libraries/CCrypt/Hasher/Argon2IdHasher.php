<?php

class CCrypt_Hasher_Argon2IdHasher extends CCrypt_Hasher_ArgonHasher {
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
        if ($this->verifyAlgorithm && $this->info($hashedValue)['algoName'] !== 'argon2id') {
            throw new RuntimeException('This password does not use the Argon2id algorithm.');
        }

        if (strlen($hashedValue) === 0) {
            return false;
        }

        return password_verify($value, $hashedValue);
    }

    /**
     * Get the algorithm that should be used for hashing.
     *
     * @return int
     */
    protected function algorithm() {
        return PASSWORD_ARGON2ID;
    }
}
