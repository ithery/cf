<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 22, 2018, 4:33:28 PM
 */

/**
 * HTTP driver for native php
 */
class CDebug_Bar_PhpHttpDriver implements CDebug_Bar_Interface_HttpDriverInterface {
    /**
     * @param array $headers
     */
    public function setHeaders(array $headers) {
        foreach ($headers as $name => $value) {
            if (!headers_sent()) {
                header($name . ':' . $value);
            }
        }
    }

    /**
     * @return bool
     */
    public function isSessionStarted() {
        return isset($_SESSION);
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function setSessionValue($name, $value) {
        $_SESSION[$name] = $value;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasSessionValue($name) {
        return array_key_exists($name, $_SESSION);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getSessionValue($name) {
        return $_SESSION[$name];
    }

    /**
     * @param string $name
     */
    public function deleteSessionValue($name) {
        unset($_SESSION[$name]);
    }
}
