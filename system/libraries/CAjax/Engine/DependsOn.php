<?php

class CAjax_Engine_DependsOn extends CAjax_Engine {
    public function execute() {
        $data = $this->ajaxMethod->getData();
        $value = carr::get($this->input, 'value');

        $dependsOn = carr::get($data, 'dependsOn');
        $dependsOn = unserialize($dependsOn);
        $errCode = 0;
        $errMessage = '';
        $resolver = $dependsOn->getResolver();
        $data = $this->invokeCallback($resolver, [$value]);

        if ($data instanceof CApp) {
            $data = $data->toArray();
        }

        if ($data instanceof CRenderable) {
            $app = c::app();
            $app->add($data);
            $data = $app->toArray();
        }

        return $this->toJsonResponse($errCode, $errMessage, $data);
    }
}
