<?php

/**
 * @internal
 */
interface CVendor_Firebase_Auth_IsTenantAwareInterface {
    /**
     * @return null|string
     */
    public function tenantId();
}
