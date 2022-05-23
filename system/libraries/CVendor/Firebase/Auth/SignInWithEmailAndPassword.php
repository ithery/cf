<?php

final class CVendor_Firebase_Auth_SignInWithEmailAndPassword implements CVendor_Firebase_Auth_IsTenantAwareInterface, CVendor_Firebase_Auth_SignInInterface {
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $clearTextPassword;

    /**
     * @var null|string
     */
    private $tenantId = null;

    /**
     * @param string $email
     * @param string $clearTextPassword
     */
    private function __construct($email, $clearTextPassword) {
        $this->email = $email;
        $this->clearTextPassword = $clearTextPassword;
    }

    /**
     * @param string $email
     * @param string $clearTextPassword
     *
     * @return self
     */
    public static function fromValues($email, $clearTextPassword) {
        return new self($email, $clearTextPassword);
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
    public function clearTextPassword() {
        return $this->clearTextPassword;
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
