<?php
trait CAuth_Concern_ImpersonateableTrait {
    /**
     * Return true or false if the user can impersonate an other user.
     *
     * @param void
     *
     * @return bool
     */
    public function canImpersonate() {
        return true;
    }

    /**
     * Return true or false if the user can be impersonate.
     *
     * @param void
     *
     * @return bool
     */
    public function canBeImpersonated() {
        return true;
    }

    /**
     * Impersonate the given user.
     *
     * @param Model       $user
     * @param null|string $guardName
     *
     * @return bool
     */
    public function startImpersonate(CModel $user, $guardName = null) {
        return CAuth::impersonateManager()->start($this, $user, $guardName);
    }

    /**
     * Check if the current user is impersonated.
     *
     * @param void
     *
     * @return bool
     */
    public function isImpersonated() {
        return CAuth::impersonateManager()->isImpersonating();
    }

    /**
     * Leave the current impersonation.
     *
     * @param void
     *
     * @return bool
     */
    public function stopImpersonate() {
        if ($this->isImpersonated()) {
            return CAuth::impersonateManager()->stop();
        }
    }
}
