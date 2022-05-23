<?php

/**
 * @see https://firebase.google.com/docs/auth/admin/manage-cookies#verify_session_cookies_using_a_third-party_jwt_library
 */
interface CVendor_Firebase_JWT_Action_VerifySessionCookie_HandlerInterface {
    /**
     * @throws CVendor_Firebase_JWT_Exception_SessionCookieVerificationFailedException
     *
     * @return CVendor_Firebase_JWT_Contract_TokenInterface
     */
    public function handle(CVendor_Firebase_JWT_Action_VerifySessionCookie $action);
}
