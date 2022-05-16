<?php

/**
 * @internal
 */
interface CVendor_Firebase_Auth_SignIn_HandlerInterface {
    /**
     * @throws CVendor_Firebase_Exception_InvalidArgumentException     If the handler does not support this action
     * @throws CVendor_Firebase_Auth_Exception_FailedToSignInException
     *
     * @return CVendor_Firebase_Auth_SignInResult
     */
    public function handle(CVendor_Firebase_Auth_SignInInterface $action);
}
