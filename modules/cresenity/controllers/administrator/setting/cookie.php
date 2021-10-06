<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 17, 2019, 7:51:20 PM
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
