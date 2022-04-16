<?php

/**
 * Built-in adapter.
 */
class CEmail_Checker_Adapter_BuiltInAdapter implements CEmail_Contract_CheckerAdapterInterface {
    protected $domains;

    public function isBlacklistedDomain($domain) {
        return in_array($domain, $this->getDomains());
    }

    private function getDomains() {
        if (null === $this->domains) {
            $this->domains = (new CEmail_Checker_BlacklistedDomain())->toArray();
        }

        return $this->domains;
    }
}
