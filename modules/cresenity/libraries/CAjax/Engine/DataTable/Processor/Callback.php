<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 2:58:36 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CAjax_Engine_DataTable_Processor_Callback extends CAjax_Engine_DataTable_Processor {

    public function process() {
        $data = $this->engine->getData();
        $callbackRequire = carr::get($data, 'callback_require');
        if (strlen($callbackRequire) > 0 && is_file($callbackRequire)) {
            require_once $callbackRequire;
        }
        $callback = carr::get($data, 'query');
        $params = array();
        $params['options'] = carr::get($data, 'callback_options');
        return call_user_func_array($callback, $params);
    }

}
