<?php

interface CValidation_Contract_UncompromisedVerifierInterface {
    /**
     * Verify that the given data has not been compromised in data leaks.
     *
     * @param array $data
     *
     * @return bool
     */
    public function verify($data);
}
