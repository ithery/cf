<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 17, 2019, 1:56:52 AM
 */
class Controller_Administrator_Setting_App extends CApp_Administrator_Controller_User {
    use CTrait_Controller_Application_Config_Editor;

    protected function getTitle() {
        return 'App Setting';
    }

    protected function getConfigGroup() {
        return 'app';
    }
}
