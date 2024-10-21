<?php

defined('SYSPATH') or die('No direct access allowed.');

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
                    if ($ruleValue instanceof CFunction_SerializableClosure) {
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

        return c::response()->json($result);
    }
}
