<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 14, 2019, 12:52:52 PM
 */
class CAjax_Engine_Validation extends CAjax_Engine {
    public function execute() {
        $data = $this->ajaxMethod->getData();
        $dataValidation = unserialize(carr::get($data, 'dataValidation'));

        $formId = carr::get($data, 'formId');

        $data = array_merge($_GET, $_POST);
        foreach ($dataValidation as $key => $rules) {
            if (is_array($rules)) {
                foreach ($rules as $ruleIndex => $ruleValue) {
                    if ($ruleValue instanceof \Opis\Closure\SerializableClosure) {
                        $dataValidation[$key][$ruleIndex] = $ruleValue->getClosure();
                    }
                }
            }
        }
        $field = '_jsvalidation';
        $factory = CValidation_Factory::instance();
        $escape = false;
        $resolver = new CJavascript_Validation_Remote_Resolver($factory, $escape);
        $factory->resolver($resolver->resolver($field));
        $factory->extend(CJavascript_Validation_Remote_Validator::EXTENSION_NAME, $resolver->validatorClosure());

        $result = $factory->validate($data, $dataValidation);
        // $remoteValidator = new CJavascript_Validation_Remote($data, $dataValidation);
        // $result = $remoteValidator->validate();

        echo json_encode($result);
    }
}
