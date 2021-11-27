<?php

class CWebSocket_Exception_ConnectionsOverCapacity extends CWebSocket_Exception {
    /**
     * Initialize the instance.
     *
     * @see    https://pusher.com/docs/pusher_protocol#error-codes
     *
     * @return void
     */
    public function __construct() {
        $this->trigger('Over capacity', 4100);
    }
}
