<?php

trait CAuth_Access_Concern_HandleAuthorizationTrait {
    /**
     * Create a new access response.
     *
     * @param null|string $message
     * @param mixed       $code
     *
     * @return \CAuth_Access_Response
     */
    protected function allow($message = null, $code = null) {
        return CAuth_Access_Response::allow($message, $code);
    }

    /**
     * Throws an unauthorized exception.
     *
     * @param null|string $message
     * @param null|mixed  $code
     *
     * @return \CAuth_Access_Response
     */
    protected function deny($message = null, $code = null) {
        return CAuth_Access_Response::deny($message, $code);
    }
}
