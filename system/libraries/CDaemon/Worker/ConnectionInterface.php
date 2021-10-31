<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 16, 2019, 6:01:13 AM
 */
interface CDaemon_Worker_ConnectionInterface {
    /**
     * Sends data on the connection.
     *
     * @param string $send_buffer
     *
     * @return void|bool
     */
    public function send($send_buffer);

    /**
     * Get remote IP.
     *
     * @return string
     */
    public function getRemoteIp();

    /**
     * Get remote port.
     *
     * @return int
     */
    public function getRemotePort();

    /**
     * Get remote address.
     *
     * @return string
     */
    public function getRemoteAddress();

    /**
     * Get local IP.
     *
     * @return string
     */
    public function getLocalIp();

    /**
     * Get local port.
     *
     * @return int
     */
    public function getLocalPort();

    /**
     * Get local address.
     *
     * @return string
     */
    public function getLocalAddress();

    /**
     * Is ipv4.
     *
     * @return bool
     */
    public function isIPv4();

    /**
     * Is ipv6.
     *
     * @return bool
     */
    public function isIPv6();

    /**
     * Close connection.
     *
     * @param $data
     *
     * @return void
     */
    public function close($data = null);
}
