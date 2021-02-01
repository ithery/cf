<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 17, 2019, 4:35:31 PM
 */

/**
 * Protocol interface
 */
interface CDaemon_Worker_ProtocolInterface {
    /**
     * Check the integrity of the package.
     * Please return the length of package.
     * If length is unknow please return 0 that mean wating more data.
     * If the package has something wrong please return false the connection will be closed.
     *
     * @param CDaemon_Worker_ConnectionInterface $connection
     * @param string                             $recv_buffer
     *
     * @return int|false
     */
    public static function input($recv_buffer, CDaemon_Worker_ConnectionInterface $connection);

    /**
     * Decode package and emit onMessage($message) callback, $message is the result that decode returned.
     *
     * @param CDaemon_Worker_ConnectionInterface $connection
     * @param string                             $recv_buffer
     *
     * @return mixed
     */
    public static function decode($recv_buffer, CDaemon_Worker_ConnectionInterface $connection);

    /**
     * Encode package brefore sending to client.
     *
     * @param CDaemon_Worker_ConnectionInterface $connection
     * @param mixed                              $data
     *
     * @return string
     */
    public static function encode($data, CDaemon_Worker_ConnectionInterface $connection);
}
