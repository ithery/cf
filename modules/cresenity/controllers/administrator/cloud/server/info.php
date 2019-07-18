<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 14, 2019, 10:26:33 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Controller_Administrator_Cloud_Server_Info extends CApp_Administrator_Controller_User {

    use CTrait_Controller_Cloud_Server_Info;

    public function index() {
        $this->info();
    }

}
