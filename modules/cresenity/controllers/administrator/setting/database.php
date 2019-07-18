<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 17, 2019, 6:46:49 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Controller_Administrator_Setting_Database extends CApp_Administrator_Controller_User {

    use CTrait_Controller_Application_Config_Editor;

    protected function getTitle() {
        return 'DB Setting';
    }

    protected function canEdit() {
        return true;
    }

    protected function getConfigGroup() {
        return 'database';
    }

}
