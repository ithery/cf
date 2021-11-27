<?php

trait CBroadcast_Trait_InteractWithSocketTrait {
    /**
     * The socket ID for the user that raised the event.
     *
     * @var null|string
     */
    public $socket;

    /**
     * Exclude the current user from receiving the broadcast.
     *
     * @return $this
     */
    public function dontBroadcastToCurrentUser() {
        $this->socket = CBroadcast::manager()->socket();

        return $this;
    }

    /**
     * Broadcast the event to everyone.
     *
     * @return $this
     */
    public function broadcastToEveryone() {
        $this->socket = null;

        return $this;
    }
}
