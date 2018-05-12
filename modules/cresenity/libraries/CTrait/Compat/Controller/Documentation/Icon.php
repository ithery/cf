<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 4:09:40 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Controller_Documentation_Icon {

    public function fa3() {
        $app = CApp::instance();
        $app->title(clang::__("Fontawesome 3.2.1 Icons"));
        $app->addTemplate()->setTemplate('Documentation/Icon/FA3');
        echo $app->render();
    }

    public function fa4() {
        $app = CApp::instance();
        $app->title(clang::__("Fontawesome 4.5.0 Icons"));
        $app->addTemplate()->setTemplate('Documentation/Icon/FA4');
        echo $app->render();
    }

}
