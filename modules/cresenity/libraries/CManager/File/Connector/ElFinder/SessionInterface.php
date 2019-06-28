<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 28, 2019, 3:11:25 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CManager_File_Connector_ElFinder_SessionInterface {

    /**
     * Session start
     *
     * @return  self
     * */
    public function start();

    /**
     * Session write & close
     *
     * @return  self
     * */
    public function close();

    /**
     * Get session data
     * This method must be equipped with an automatic start / close.
     *
     * @param   string $key   Target key
     * @param   mixed  $empty Return value of if session target key does not exist
     *
     * @return  mixed
     * */
    public function get($key, $empty = '');

    /**
     * Set session data
     * This method must be equipped with an automatic start / close.
     *
     * @param   string $key  Target key
     * @param   mixed  $data Value
     *
     * @return  self
     * */
    public function set($key, $data);

    /**
     * Get session data
     *
     * @param   string $key Target key
     *
     * @return  self
     * */
    public function remove($key);
}
