<?php
use Cresenity\Demo\DemoVariable;

class Controller_Demo_Account_Theme extends \Cresenity\Demo\Controller {
    public function change($theme) {
        $app = c::app();
        $themeData = DemoVariable::themeData();
        $nextTheme = carr::get($themeData, $theme);
        if ($nextTheme != null) {
            c::session()->set('theme', $theme);
        }

        return c::redirect()->back();
    }
}
