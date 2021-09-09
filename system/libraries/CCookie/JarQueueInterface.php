<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 3:37:16 PM
 */
interface CCookie_JarQueueInterface extends CCookie_JarInterface {
    /**
     * Queue a cookie to send with the next response.
     *
     * @param array $parameters
     *
     * @return void
     */
    public function queue(...$parameters);

    /**
     * Remove a cookie from the queue.
     *
     * @param string $name
     */
    public function unqueue($name);

    /**
     * Get the cookies which have been queued for the next request.
     *
     * @return array
     */
    public function getQueuedCookies();
}
