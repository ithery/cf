<?php

class CWebSocket_Exception_UnknownAppKey extends CWebSocket_Exception {
    /**
     * Initalize the exception.
     *
     * @param string $appKey
     *
     * @return void
     */
    public function __construct($appKey) {
        $this->trigger("Could not find app key `{$appKey}`.", 4001);
    }
}
