<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 22, 2018, 4:33:57 PM
 */

/**
 * Provides an abstraction of PHP native features for easier integration
 * in third party frameworks.
 */
interface CDebug_DebugBar_Interface_HttpDriverInterface {
    /**
     * Sets HTTP headers.
     *
     * @param array $headers
     */
    public function setHeaders(array $headers);

    /**
     * Checks if the session is started.
     *
     * @return bool
     */
    public function isSessionStarted();

    /**
     * Sets a value in the session.
     *
     * @param string $name
     * @param string $value
     */
    public function setSessionValue($name, $value);

    /**
     * Checks if a value is in the session.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasSessionValue($name);

    /**
     * Returns a value from the session.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getSessionValue($name);

    /**
     * Deletes a value from the session.
     *
     * @param string $name
     */
    public function deleteSessionValue($name);
}
