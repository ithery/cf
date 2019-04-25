<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 14, 2019, 12:52:52 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CAjax_Engine_Validation extends CAjax_Engine {

    public function execute() {
        $data = $this->ajaxMethod->getData();
        $dataValidation = carr::get($data, 'dataValidation');
        $formId = carr::get($data, 'formId');
        $formId = carr::get($data, '');

        $data = array_merge($_GET, $_POST);

        $remoteValidator = new CJavascript_Validation_Remote($data, $dataValidation);
        $result = $remoteValidator->validate();
        
        echo json_encode($result);
    }

}
