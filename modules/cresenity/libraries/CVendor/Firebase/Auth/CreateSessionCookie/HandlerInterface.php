<?php

interface CVendor_Firebase_Auth_CreateSessionCookie_HandlerInterface {
    /**
     * @throws CVendor_Firebase_Auth_Exception_FailedToCreateSessionCookieException
     *
     * @return string
     */
    public function handle(CVendor_Firebase_Auth_CreateSessionCookie $action);
}
