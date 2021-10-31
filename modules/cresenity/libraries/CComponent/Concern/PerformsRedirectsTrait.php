<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 29, 2020
 */
trait CComponent_Concern_PerformsRedirectsTrait {
    public $redirectTo;

    public function redirect($url) {
        $this->redirectTo = $url;
    }

    public function redirectRoute($name, $parameters = [], $absolute = true) {
        $this->redirectTo = c::route($name, $parameters, $absolute);
    }

    public function redirectAction($name, $parameters = [], $absolute = true) {
        $this->redirectTo = c::action($name, $parameters, $absolute);
    }
}
