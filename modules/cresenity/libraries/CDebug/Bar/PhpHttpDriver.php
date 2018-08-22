<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 22, 2018, 4:33:28 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * HTTP driver for native php
 */
class CDebug_Bar_PhpHttpDriver implements CDebug_Bar_Interface_HttpDriverInterface {

    /**
     * @param array $headers
     */
    function setHeaders(array $headers) {
        foreach ($headers as $name => $value) {
            header("$name: $value");
        }
    }

    /**
     * @return bool
     */
    function isSessionStarted() {
        return isset($_SESSION);
    }

    /**
     * @param string $name
     * @param string $value
     */
    function setSessionValue($name, $value) {
        $_SESSION[$name] = $value;
    }

    /**
     * @param string $name
     * @return bool
     */
    function hasSessionValue($name) {
        return array_key_exists($name, $_SESSION);
    }

    /**
     * @param string $name
     * @return mixed
     */
    function getSessionValue($name) {
        return $_SESSION[$name];
    }

    /**
     * @param string $name
     */
    function deleteSessionValue($name) {
        unset($_SESSION[$name]);
    }

}
