<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 22, 2018, 12:31:46 PM
 */
class Controller_Administrator_Stats_Phpinfo extends CApp_Administrator_Controller_User {
    use CTrait_Controller_Application_Server_PhpInfo;

    public function index() {
        return $this->phpinfo();
    }
}
