<?php

/**
 * @see https://firebase.google.com/docs/auth/admin/verify-id-tokens#verify_id_tokens_using_a_third-party_jwt_library
 */
interface CVendor_Firebase_JWT_Action_VerifyIdToken_HandlerInterface {
    /**
     * @throws CVendor_Firebase_JWT_Exception_IdTokenVerificationFailedException
     *
     * @return CVendor_Firebase_JWT_Contract_TokenInterface
     */
    public function handle(CVendor_Firebase_JWT_Action_VerifyIdToken $action);
}
