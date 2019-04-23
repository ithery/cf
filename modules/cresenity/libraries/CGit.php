<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 24, 2019, 1:53:09 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CGit {

    /**
     * 
     * @param string $path
     * @return CGit_Repository
     */
    public static function getRepository($path) {
        $client = new CGit_Client();
        return $client->getRepository($path);
    }

}
