<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 14, 2019, 7:37:07 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CAjax_Engine_Reload extends CAjax_Engine {

    public function execute() {
        $input = $this->input;
        $data = $this->ajaxMethod->getData();
        $json = carr::get($data, 'json');
        $callback = carr::get($data, 'callback');
        if ($callback != null) {
            $callback = CHelper::closure()->deserializeClosure($callback);
            $parameters=array();
            call_user_func_array($callback, $parameters);
            return CApp::instance()->json();
        } else {


            return $json;
        }
    }

}
