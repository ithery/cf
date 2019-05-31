<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 16, 2019, 4:26:12 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CSocialLogin_AbstractProvider implements CSocialLogin_AbstractProviderInterface {

    public function input($key = null, $default = null) {
        $request = CApp_Base::getRequestGet() + CApp_Base::getRequestPost();
        return carr::get($request, $key, $default);
    }

    public function session() {
        return CSession::instance();
    }

    public function hasRequest($key) {
        $request = CApp_Base::getRequestGet() + CApp_Base::getRequestPost();
        return isset($request[$key]);
    }

}
