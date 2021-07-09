<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
trait CComponent_Concern_PerformsRedirectsTrait {

    public $redirectTo;

    public function redirect($url) {
        $this->redirectTo = $url;
    }

    public function redirectRoute($name, $parameters = [], $absolute = true) {
        $this->redirectTo = route($name, $parameters, $absolute);
    }

    public function redirectAction($name, $parameters = [], $absolute = true) {
        $this->redirectTo = action($name, $parameters, $absolute);
    }

}
