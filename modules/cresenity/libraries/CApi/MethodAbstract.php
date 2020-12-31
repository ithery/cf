<?php

/**
 * Description of MethodAbstract
 *
 * @author Hery
 */
abstract class CApi_MethodAbstract implements CInterface_Arrayable {
    protected $errCode = 0;
    protected $errMessage = '';
    protected $data = [];

    abstract public function execute();

    public function toArray() {
        return [
            'errCode' => (int) $this->errCode,
            'errMessage' => (string) $this->errMessage,
            'data' => $this->data,
        ];
    }
}
