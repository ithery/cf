<?php

interface CWebSocket_Contract_PusherMessageInterface {
    /**
     * Respond to the message construction.
     *
     * @return void
     */
    public function respond();
}
