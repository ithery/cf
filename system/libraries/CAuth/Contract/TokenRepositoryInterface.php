<?php

interface CAuth_Contract_TokenRepositoryInterface {
    /**
     * Create a new token.
     *
     * @param \CAuth_Contract_CanResetPasswordInterface $user
     *
     * @return string
     */
    public function create(CAuth_Contract_CanResetPasswordInterface $user);

    /**
     * Determine if a token record exists and is valid.
     *
     * @param \CAuth_Contract_CanResetPasswordInterface $user
     * @param string                                    $token
     *
     * @return bool
     */
    public function exists(CAuth_Contract_CanResetPasswordInterface $user, $token);

    /**
     * Determine if the given user recently created a password reset token.
     *
     * @param \CAuth_Contract_CanResetPasswordInterface $user
     *
     * @return bool
     */
    public function recentlyCreatedToken(CAuth_Contract_CanResetPasswordInterface $user);

    /**
     * Delete a token record.
     *
     * @param \CAuth_Contract_CanResetPasswordInterface $user
     *
     * @return void
     */
    public function delete(CAuth_Contract_CanResetPasswordInterface $user);

    /**
     * Delete expired tokens.
     *
     * @return void
     */
    public function deleteExpired();
}
