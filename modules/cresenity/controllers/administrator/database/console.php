<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 14, 2018, 7:46:44 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Controller_Administrator_Database_Console extends CApp_Administrator_Controller_User {

    public function index() {
        $app = CApp::instance();
        $app->title(clang::__("Database Console"));
        $app->manager()->registerModule('terminal');

        $html = CView::factory('admin/page/console/db/html');
        $html = $html->render();
        $js = CView::factory('admin/page/console/db/js');
        $js = $js->render();
        $app->add($html);
        $app->add_js($js);


        echo $app->render();
    }

}

// End Home Controller