<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 23, 2018, 1:49:41 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CHelper_Request {

    /**
     * Tests if the current request is an AJAX request by checking the X-Requested-With HTTP
     * request header that most popular JS frameworks now set for AJAX calls.
     *
     * @return  boolean
     */
    public static function isAjax() {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

}
