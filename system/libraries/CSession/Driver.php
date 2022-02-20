<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * CSession driver interface.
 */
interface CSession_Driver extends SessionHandlerInterface {
    /**
     * Opens a session.
     *
     * @param string $path save path
     * @param string $name session name
     *
     * @return bool
     */
    public function open($path, $name);

    /**
     * Closes a session.
     *
     * @return bool
     */
    public function close();

    /**
     * Reads a session.
     *
     * @param string $id session id
     *
     * @return string
     */
    public function read($id);

    /**
     * Writes a session.
     *
     * @param string $id   session id
     * @param string $data session data
     *
     * @return bool
     */
    public function write($id, $data);

    /**
     * Destroys a session.
     *
     * @param string $id session id
     *
     * @return bool
     */
    public function destroy($id);

    /**
     * Regenerates the session id.
     *
     * @return string
     */
    public function regenerate();

    /**
     * Garbage collection.
     *
     * @param int $maxlifetime session expiration period
     *
     * @return bool
     */
    public function gc($maxlifetime);
}

// End Session Driver Interface
