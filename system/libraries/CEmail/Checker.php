<?php

class CEmail_Checker {
    /**
     * @param AdapterInterface $adapter Checker adapter
     */
    public function __construct(CEmail_Contract_CheckerAdapterInterface $adapter = null) {
        $this->adapter = $adapter ?: new CEmail_Checker_Adapter_BuiltInAdapter();
    }

    /**
     * Check if it's a valid email, ie. not a throwaway email.
     *
     * @param string $email The email to check
     *
     * @return bool true for a throwaway email
     */
    public function isValid($email) {
        if (false === $email = filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        try {
            list($local, $domain) = CEmail_Helper::parseEmailAddress($email);
        } catch (CEmail_Exception_InvalidEmailException $e) {
            return false;
        }

        return !$this->adapter->isThrowawayDomain($domain);
    }
}
