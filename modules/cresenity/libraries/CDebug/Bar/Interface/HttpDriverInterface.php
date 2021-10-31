<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 22, 2018, 4:33:57 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Provides an abstraction of PHP native features for easier integration
 * in third party frameworks
 */
interface CDebug_Bar_Interface_HttpDriverInterface {

    /**
     * Sets HTTP headers
     *
     * @param array $headers
     * @return
     */
    function setHeaders(array $headers);

    /**
     * Checks if the session is started
     *
     * @return boolean
     */
    function isSessionStarted();

    /**
     * Sets a value in the session
     *
     * @param string $name
     * @param string $value
     */
    function setSessionValue($name, $value);

    /**
     * Checks if a value is in the session
     *
     * @param string $name
     * @return boolean
     */
    function hasSessionValue($name);

    /**
     * Returns a value from the session
     *
     * @param string $name
     * @return mixed
     */
    function getSessionValue($name);

    /**
     * Deletes a value from the session
     *
     * @param string $name
     */
    function deleteSessionValue($name);
}
