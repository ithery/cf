<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 10, 2019, 7:17:48 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Controller_Administrator_Cloud_Project_Info extends CApp_Administrator_Controller_User {

    public function index() {
        $app = CApp::instance();
        $errCode = 0;
        $errMessage = '';
        $cloudData = array();
        $app->title('Dev Cloud Information');

        $tabActive = 'info';
        if (isset($_GET['tab'])) {
            $tabActive = $_GET['tab'];
        }
        $tabProjectActive = $tabActive == 'project';
        $tabAppActive = $tabActive == 'app';

        $tabList = $app->addTabList();
        $tabList->addTab()->setLabel('Project')->setAjaxUrl(curl::base() . 'administrator/cloud/info/project/tab')
                ->setActive($tabProjectActive);

        $tabList->addTab()->setLabel('Application')->setAjaxUrl(curl::base() . 'administrator/cloud/info/app/tab')
                ->setActive($tabAppActive)->setNoPadding();

        try {
            $cloudData = CApp_Cloud::instance()->api('Development/GetInfo');
        } catch (Exception $ex) {
            $errCode++;
            $errMessage = $ex->getMessage();
        }
        if ($errCode > 0) {
            $app->message('error', $errMessage);
        }

        $app->add($cloudData);


        echo $app->render();
    }

}
