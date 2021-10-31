<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 11, 2019, 12:51:36 AM
 */
class Controller_Administrator_App_File extends CApp_Administrator_Controller_User {
    public function index() {
        $app = CApp::instance();
        $app->title('File Manager');
        $fileManager = $app->addFileManager();
        echo $app->render();
    }
}
