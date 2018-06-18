<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 19, 2018, 4:31:53 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Api interface
 */
interface CGitlab_ApiInterface {

    public function __construct(CGitlab_Client $client);
}
