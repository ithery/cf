<?php
class CBroadcast_Channel_PrivateChannel extends CBroadcast_Channel {
    /**
     * Create a new channel instance.
     *
     * @param \CBroadcast_Contract_HasBroadcastChannelInterface|string $name
     *
     * @return void
     */
    public function __construct($name) {
        $name = $name instanceof CBroadcast_Contract_HasBroadcastChannelInterface ? $name->broadcastChannel() : $name;

        parent::__construct('private-' . $name);
    }
}
