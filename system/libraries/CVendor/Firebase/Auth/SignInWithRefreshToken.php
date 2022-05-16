<?php

final class CVendor_Firebase_Auth_SignInWithRefreshToken implements CVendor_Firebase_Auth_IsTenantAwareInterface, CVendor_Firebase_Auth_SignInInterface {
    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var null|string
     */
    private $tenantId = null;

    /**
     * @param string $refreshToken
     */
    private function __construct($refreshToken) {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @param string $refreshToken
     *
     * @return self
     */
    public static function fromValue($refreshToken) {
        return new self($refreshToken);
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
     * @return string
     */
    public function refreshToken() {
        return $this->refreshToken;
    }

    /**
     * @return null|string
     */
    public function tenantId() {
        return $this->tenantId;
    }
}
