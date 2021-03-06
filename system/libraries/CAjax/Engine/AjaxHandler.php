<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 14, 2019, 7:37:07 PM
 */
class CAjax_Engine_AjaxHandler extends CAjax_Engine {
    public function execute() {
        $input = $this->input;
        $data = $this->ajaxMethod->getData();
        $callback = carr::get($data, 'callback');
        $json = carr::get($data, 'json');

        if ($callback != null) {
            $app = CApp::instance();
            $parameters = [$app];

            return c::value($callback, ...$parameters);
        } else {
            return $json;
        }
    }
}
