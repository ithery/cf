<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 15, 2018, 12:38:34 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CAjax_Engine_Callback extends CAjax_Engine {

    public function execute() {
        $data = $this->ajaxMethod->getData();
        $callable = carr::get($data, 'callable');
        $requires = carr::get($data, 'requires');
        if (!is_array($requires)) {
            $requires = array($requires);
        }

        foreach ($requires as $require) {
            if (strlen($require) > 0 && file_exists($require)) {
                require_once $require;
            }
        }
        if (is_callable($callable)) {
            return call_user_func($callable, $data);
        }

        return false;
    }

}
