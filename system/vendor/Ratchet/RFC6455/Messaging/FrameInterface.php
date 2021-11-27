<?php

namespace Ratchet\RFC6455\Messaging;

interface FrameInterface extends DataInterface {
    /**
     * Add incoming data to the frame from peer.
     *
     * @param string
     * @param mixed $buf
     */
    public function addBuffer($buf);

    /**
     * Is this the final frame in a fragmented message?
     *
     * @return bool
     */
    public function isFinal();

    /**
     * Is the payload masked?
     *
     * @return bool
     */
    public function isMasked();

    /**
     * @return int
     */
    public function getOpcode();

    /**
     * @return int
     */
    //function getReceivedPayloadLength();

    /**
     * 32-big string.
     *
     * @return string
     */
    public function getMaskingKey();
}
