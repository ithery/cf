<?php
class CBroadcast_Channel_PresenceChannel extends CBroadcast_Channel {
    /**
     * Create a new channel instance.
     *
     * @param string $name
     *
     * @return void
     */
    public function __construct($name) {
        parent::__construct('presence-' . $name);
    }
}
