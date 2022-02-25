<?php

interface CVendor_Firebase_JWT_Action_FetchGooglePublicKeys_HandlerInterface {
    /**
     * @throws CVendor_Firebase_JWT_Exception_FetchingGooglePublicKeysFailedException
     *
     * @return CVendor_Firebase_JWT_Contract_KeysInterface
     */
    public function handle(CVendor_Firebase_JWT_Action_FetchGooglePublicKeys $action);
}
