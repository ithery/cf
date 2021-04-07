<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Jul 7, 2020
 */
trait CTrait_Controller_Application_Server_PhpInfo {
    public function phpinfo() {
        $app = CApp::instance();

        $app->title(clang::__('PHP Info'));

        $html = CView::factory('admin/page/phpinfo/html');
        $html = $html->render();
        $js = CView::factory('admin/page/phpinfo/js');
        $js = $js->render();
        $app->add($html);
        $app->addJs($js);

        echo $app->render();
    }
}
