<?php

interface CVendor_Firebase_Auth_CreateActionLink_HandlerInterface {
    /**
     * @throws CVendor_Firebase_Auth_Exception_FailedToCreateActionLinkException
     *
     * @return string
     */
    public function handle(CVendor_Firebase_Auth_CreateActionLink $action);
}
