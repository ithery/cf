<?php

interface CWebsocketClientEngineInterface {
    const OPEN    = 0;
    const CLOSE   = 1;
    const PING    = 2;
    const PONG    = 3;
    const MESSAGE = 4;
    const UPGRADE = 5;
    const NOOP    = 6;
    /** Connect to the targeted server */
    public function connect();
    /** Closes the connection to the websocket */
    public function close();
    /**
     * Read data from the socket
     *
     * @return string Data read from the socket
     */
    public function read();
    /**
     * Emits a message through the websocket
     *
     * @param string $event Event to emit
     * @param array  $args  Arguments to send
     */
    public function emit($event, array $args);
    /** Keeps alive the connection */
    public function keep_alive();
    /** Gets the name of the engine */
    public function get_name();
}