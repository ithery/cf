<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 15, 2018, 12:38:34 AM
 */
class CAjax_Engine_Callback extends CAjax_Engine {
    public function execute() {
        $data = $this->ajaxMethod->getData();
        $callable = carr::get($data, 'callable');
        $requires = carr::get($data, 'requires');
        $result = CFunction::factory($callable)->addArg($data)->setRequire($requires)->execute();
        return $result;
    }
}
