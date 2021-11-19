<?php

class UnsubscribedFromChannel {
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
     * The channel name.
     *
     * @var string
     */
    public $channelName;

    /**
     * The user received on presence channel.
     *
     * @var string
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param string        $appId
     * @param string        $socketId
     * @param string        $channelName
     * @param null|stdClass $user
     *
     * @return void
     */
    public function __construct($appId, $socketId, $channelName, $user = null) {
        $this->appId = $appId;
        $this->socketId = $socketId;
        $this->channelName = $channelName;
        $this->user = $user;
    }
}
