<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Message_Controller extends CController {

    public function index() {

        $app = CApp::instance();
        $app->title(clang::__("Message"));
        $app->show_title(false);
        $app->show_breadcrumb(false);
        $org = $app->org();
//		if($org!=null) {

        $vi = 'sales_carousel';

        $html = CView::factory('cwidget/message/html');
        $html = $html->render();
        $js = CView::factory('cwidget/message/js');
        $js = $js->render();
        $app->add($html);
        $app->add_js($js);

//		}
        echo $app->render();
    }

}
