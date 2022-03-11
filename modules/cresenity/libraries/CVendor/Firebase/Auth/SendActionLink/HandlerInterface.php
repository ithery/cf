<?php

interface CVendor_Firebase_Auth_SendActionLink_HandlerInterface {
    /**
     * @throws CVendor_Firebase_Auth_Exception_FailedToSendActionLinkException
     */
    public function handle(CVendor_Firebase_Auth_SendActionLink $action);
}
