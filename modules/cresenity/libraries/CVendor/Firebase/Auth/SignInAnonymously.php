<?php

final class CVendor_Firebase_Auth_SignInAnonymously implements CVendor_Firebase_Auth_SignInInterface {
    /**
     * @var null|string
     */
    private $tenantId = null;

    private function __construct() {
    }

    /**
     * @return self
     */
    public static function new() {
        return new self();
    }

    /**
     * @param string $tenantId
     *
     * @return self
     */
    public function withTenantId($tenantId) {
        $action = clone $this;
        $action->tenantId = $tenantId;

        return $action;
    }

    /**
     * @return null|string
     */
    public function tenantId() {
        return $this->tenantId;
    }
}
