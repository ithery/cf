<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 22, 2018, 12:31:46 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Controller_Administrator_Stats_Phpinfo extends CApp_Administrator_Controller_User {

    public function index() {
        $app = CApp::instance();
        $app->set_login_required(false);
        $app = CApp::instance();
        $app->title(clang::__("PHP Info"));

        $html = CView::factory('admin/page/phpinfo/html');
        $html = $html->render();
        $js = CView::factory('admin/page/phpinfo/js');
        $js = $js->render();
        $app->add($html);
        $app->add_js($js);

        echo $app->render();
    }

}
