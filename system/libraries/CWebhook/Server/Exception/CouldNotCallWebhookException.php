<?php

class CWebhook_Server_Exception_CouldNotCallWebhookException extends Exception {
    /**
     * @return self
     */
    public static function urlNotSet() {
        return new static('Could not call the webhook because the url has not been set.');
    }

    /**
     * @return self
     */
    public static function secretNotSet() {
        return new static('Could not call the webhook because no secret has been set.');
    }
}
