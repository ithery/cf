<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Api interface
 *
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 19, 2018, 4:31:53 AM
 */
interface CGitlab_ApiInterface {
    public function __construct(CGitlab_Client $client);
}
