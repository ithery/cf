<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 17, 2019, 7:51:20 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Controller_Administrator_Setting_Cookie extends CApp_Administrator_Controller_User {

    use CTrait_Controller_Application_Config_Editor;

    protected function getTitle() {
        return 'Cookie Setting';
    }

    protected function canEdit() {
        return true;
    }

    protected function getConfigGroup() {
        return 'cookie';
    }

}
