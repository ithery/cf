<?php

defined('SYSPATH') or die('No direct access allowed.');

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
