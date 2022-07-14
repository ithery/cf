<?php

class CWebhook_Client_Exception_InvalidWebhookSignatureException extends Exception {
    /**
     * @return self
     */
    public static function make() {
        return new static('The signature is invalid.');
    }
}
