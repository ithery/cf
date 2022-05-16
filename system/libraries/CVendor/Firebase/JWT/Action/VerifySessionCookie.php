<?php

final class CVendor_Firebase_JWT_Action_VerifySessionCookie {
    /**
     * @var string
     */
    private $sessionCookie = '';

    /**
     * @var int
     */
    private $leewayInSeconds = 0;

    /**
     * @var null|string
     */
    private $expectedTenantId = null;

    private function __construct() {
    }

    /**
     * @param string $sessionCookie
     *
     * @return self
     */
    public static function withSessionCookie($sessionCookie) {
        $action = new self();
        $action->sessionCookie = $sessionCookie;

        return $action;
    }

    /**
     * @param string $tenantId
     *
     * @return self
     */
    public function withExpectedTenantId($tenantId) {
        $action = clone $this;
        $action->expectedTenantId = $tenantId;

        return $action;
    }

    /**
     * @param int $seconds
     *
     * @return self
     */
    public function withLeewayInSeconds($seconds) {
        if ($seconds < 0) {
            throw new InvalidArgumentException('Leeway must not be negative');
        }

        $action = clone $this;
        $action->leewayInSeconds = $seconds;

        return $action;
    }

    /**
     * @return string
     */
    public function sessionCookie() {
        return $this->sessionCookie;
    }

    /**
     * @return null|string
     */
    public function expectedTenantId() {
        return $this->expectedTenantId;
    }

    /**
     * @return int
     */
    public function leewayInSeconds() {
        return $this->leewayInSeconds;
    }
}
