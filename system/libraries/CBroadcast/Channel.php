<?php

class CBroadcast_Channel {
    /**
     * The channel's name.
     *
     * @var string
     */
    public $name;

    /**
     * Create a new channel instance.
     *
     * @param \CBroadcast_Contract_HasBroadcastChannelInterface|string $name
     *
     * @return void
     */
    public function __construct($name) {
        $this->name = $name instanceof CBroadcast_Contract_HasBroadcastChannelInterface ? $name->broadcastChannel() : $name;
    }

    /**
     * Convert the channel instance to a string.
     *
     * @return string
     */
    public function __toString() {
        return $this->name;
    }
}
