<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 25, 2019, 8:56:22 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * This interface describes the minimum interface needed for the transport object used by the
 * CXMLRPC_Client
 * @package CXMLRPC
 */
interface CXMLRPC_TransportInterface {

    /**
     * This method must post the request to the given url and return the results.
     * @param string $url The url to post to.
     * @param string $request The request to post.
     * @return string The server response
     */
    public function post($url, $request);
}
