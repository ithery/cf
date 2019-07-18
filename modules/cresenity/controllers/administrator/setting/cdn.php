<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 17, 2019, 1:54:14 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Controller_Administrator_Setting_Cdn extends CApp_Administrator_Controller_User {

    use CTrait_Controller_Application_Config_Editor;

    protected function getTitle() {
        return 'CDN Setting';
    }

    protected function getConfigGroup() {
        return 'cdn';
    }

    protected function canEdit() {
        return true;
    }

}
