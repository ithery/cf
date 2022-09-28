<?php

abstract class CServer_SMTP_Auth_Handler {
    /**
     * Decode the credentials from the client.
     *
     * @param array $credentials
     *
     * @return array
     */
    public function decodeCredentials(array $credentials): array {
        return [
            'user' => base64_decode(carr::get($credentials, 'user')),
            'password' => base64_decode(carr::get($credentials, 'password')),
        ];
    }

    /**
     * Attempt to authenticate a user when logging in via SMTP.
     *
     * @param array $credentials
     *
     * @return null|CAuth_AuthenticatableInterface
     */
    abstract public function attempt(array $credentials);
}
