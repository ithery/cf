<?php
class CWebSocket_Event_ConnectionPonged {
    use CEvent_Trait_Dispatchable, CQueue_Trait_SerializesModels;

    /**
     * The WebSockets app id that the user connected to.
     *
     * @var string
     */
    public $appId;

    /**
     * The Socket ID associated with the connection.
     *
     * @var string
     */
    public $socketId;

    /**
     * Create a new event instance.
     *
     * @param string $appId
     * @param string $socketId
     *
     * @return void
     */
    public function __construct($appId, $socketId) {
        $this->appId = $appId;
        $this->socketId = $socketId;
    }
}
