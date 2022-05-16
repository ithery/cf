<?php

final class CVendor_Firebase_Auth_SignInWithEmailAndOobCode implements CVendor_Firebase_Auth_IsTenantAwareInterface, CVendor_Firebase_Auth_SignInInterface {
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $oobCode;

    /**
     * @var null|string
     */
    private $tenantId = null;

    /**
     * @param string $email
     * @param string $oobCode
     */
    private function __construct($email, $oobCode) {
        $this->email = $email;
        $this->oobCode = $oobCode;
    }

    /**
     * @param string $email
     * @param string $oobCode
     *
     * @return self
     */
    public static function fromValues($email, $oobCode) {
        return new self($email, $oobCode);
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
    public function email() {
        return $this->email;
    }

    /**
     * @return string
     */
    public function oobCode() {
        return $this->oobCode;
    }

    /**
     * @return null|string
     */
    public function tenantId() {
        return $this->tenantId;
    }
}
