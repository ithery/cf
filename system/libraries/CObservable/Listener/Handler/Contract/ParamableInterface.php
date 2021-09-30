<?php


interface CObservable_Listener_Handler_Contract_ParamableInterface {
    public function getParams();

    public function setParams(array $params);

    public function addParam($key, $value);
}
