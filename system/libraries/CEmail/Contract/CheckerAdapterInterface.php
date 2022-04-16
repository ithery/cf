<?php

/**
 * Adapter used to check blacklisted domains.
 */
interface CEmail_Contract_CheckerAdapterInterface {
    /**
     * Checks if an email domain is throwaway.
     *
     * @param string $domain The domain to check
     *
     * @return bool True for a blacklisted domain
     */
    public function isBlacklistedDomain($domain);
}
