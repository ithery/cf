<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 17, 2019, 4:35:31 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Protocol interface
 */
interface CDaemon_Service_Listener_ProtocolInterface {

    /**
     * Check the integrity of the package.
     * Please return the length of package.
     * If length is unknow please return 0 that mean wating more data.
     * If the package has something wrong please return false the connection will be closed.
     *
     * @param ConnectionInterface $connection
     * @param string              $recv_buffer
     * @return int|false
     */
    public static function input($recv_buffer, ConnectionInterface $connection);

    /**
     * Decode package and emit onMessage($message) callback, $message is the result that decode returned.
     *
     * @param ConnectionInterface $connection
     * @param string              $recv_buffer
     * @return mixed
     */
    public static function decode($recv_buffer, ConnectionInterface $connection);

    /**
     * Encode package brefore sending to client.
     *
     * @param ConnectionInterface $connection
     * @param mixed               $data
     * @return string
     */
    public static function encode($data, ConnectionInterface $connection);
}
