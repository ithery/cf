<?php

interface CBroadcast_Contract_BroadcasterInterface {
    /**
     * Authenticate the incoming request for a given channel.
     *
     * @param \CHTTP_Request $request
     *
     * @return mixed
     */
    public function auth($request);

    /**
     * Return the valid authentication response.
     *
     * @param CHTTP_Request $request
     * @param mixed         $result
     *
     * @return mixed
     */
    public function validAuthenticationResponse($request, $result);

    /**
     * Broadcast the given event.
     *
     * @param array  $channels
     * @param string $event
     * @param array  $payload
     *
     * @return void
     */
    public function broadcast(array $channels, $event, array $payload = []);
}
