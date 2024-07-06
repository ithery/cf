<?php

class CReport_Jasper_Instruction {
    protected $type;

    protected $params;

    public function __construct($type, $params) {
        $this->type = $type;
        $this->params = $params;
    }

    public function method() {
        $typeMethodMap = [
            'break' => 'breaker'
        ];
        $method = carr::get($typeMethodMap, $this->type, $this->type);

        return $method;
    }

    public function run(CReport_Jasper_ProcessorAbstract $processor) {
        $method = $this->method();
        if (method_exists($processor, $method)) {
            $processor->$method($this->params);
        } else {
            throw new Exception('Method name ' . $method . 'is not exists on ' . get_class($processor));
        }
    }
}
