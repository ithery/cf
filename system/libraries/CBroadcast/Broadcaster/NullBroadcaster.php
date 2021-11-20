<?php
class CBroadcast_Broadcaster_NullBroadcaster extends CBroadcast_BroadcasterAbstract {
    /**
     * @inheritdoc
     */
    public function auth($request) {
    }

    /**
     * @inheritdoc
     */
    public function validAuthenticationResponse($request, $result) {
    }

    /**
     * @inheritdoc
     */
    public function broadcast(array $channels, $event, array $payload = []) {
    }
}
