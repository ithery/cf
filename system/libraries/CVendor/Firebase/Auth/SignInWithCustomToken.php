<?php

final class CVendor_Firebase_Auth_SignInWithCustomToken implements CVendor_Firebase_Auth_IsTenantAwareInterface, CVendor_Firebase_Auth_SignInInterface {
    /**
     * @var string
     */
    private $customToken;

    /**
     * @var null|string
     */
    private $tenantId = null;

    /**
     * @param string $customToken
     */
    private function __construct($customToken) {
        $this->customToken = $customToken;
    }

    /**
     * @param string $customToken
     *
     * @return self
     */
    public static function fromValue($customToken) {
        return new self($customToken);
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
    public function customToken() {
        return $this->customToken;
    }

    /**
     * @return null|string
     */
    public function tenantId() {
        return $this->tenantId;
    }
}
