<?php
interface CAuth_Contract_ImpersonateableInterface {
    /**
     * Return true or false if the user can impersonate an other user.
     *
     * @param void
     *
     * @return bool
     */
    public function canImpersonate();

    /**
     * Return true or false if the user can be impersonate.
     *
     * @param void
     *
     * @return bool
     */
    public function canBeImpersonated();

    /**
     * Impersonate the given user.
     *
     * @param CModel      $user
     * @param null|string $guardName
     *
     * @return bool
     */
    public function startImpersonate(CModel $user, $guardName = null);

    /**
     * Check if the current user is impersonated.
     *
     * @return bool
     */
    public function isImpersonated();

    /**
     * Leave the current impersonation.
     *
     * @return bool
     */
    public function stopImpersonate();
}
