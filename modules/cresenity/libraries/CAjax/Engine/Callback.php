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
       



        $result = CFunction::factory($callable)->setArgs($data)->setRequire($requires)->execute();


        return $result;
    }

}
