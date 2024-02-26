<?php

defined('SYSPATH') or die('No direct access allowed.');

class CAjax_Engine_Callback extends CAjax_Engine {
    public function execute() {
        $data = $this->ajaxMethod->getData();
        $callable = carr::get($data, 'callable');
        $requires = carr::get($data, 'requires');
        $result = CFunction::factory($callable)->addArg($data)->setRequire($requires)->execute();

        if (CF::domain() == 'maxpage.staging.ittron.co.id') {
            cdbg::dd($result);
        }

        return $result;
    }
}
