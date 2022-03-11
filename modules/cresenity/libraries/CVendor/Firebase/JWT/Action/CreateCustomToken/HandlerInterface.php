<?php

interface CVendor_Firebase_JWT_Action_CreateCustomToken_HandlerInterface {
    /**
     * @throws CVendor_Firebase_JWT_Exception_CustomTokenCreationFailedException
     *
     * @return CVendor_Firebase_JWT_Contract_TokenInterface
     */
    public function handle(CVendor_Firebase_JWT_Action_CreateCustomToken $action);
}
