<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 7:14:18 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Controller_Documentation_Geo_Ip {

    public function index() {
        $app = CApp::instance();

        $app->title('Geo IP Address');


        $code = 'CGeo::ip()->getClientIP();';
        $app->addDiv()->addClass('my-2 console')->add($code);

        $result = CGeo::ip()->getClientIP();
        $app->addDiv()->addClass('my-2 json-container')->add($result);

        $code = 'CGeo::ip()->getLocation(CGeo::ip()->getClientIP());';
        $app->addDiv()->addClass('my-2 console')->add($code);

        $result = CGeo::ip()->getLocation();
        $app->addDiv()->addClass('my-2 json-container')->add($result);

        echo $app->render();
    }

}
