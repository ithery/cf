<?php

class CWebSocket_Exception_OriginNotAllowed extends CWebSocket_Exception {
    /**
     * Initalize the exception.
     *
     * @param string $appKey
     *
     * @return void
     */
    public function __construct($appKey) {
        $this->trigger("The origin is not allowed for `{$appKey}`.", 4009);
    }
}
