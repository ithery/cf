<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 7:14:18 PM
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
