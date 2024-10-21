<?php

class CReport_Jasper_InstructionRepository {
    /**
     * @var CReport_Jasper_Instruction[]
     */
    protected $instructions;

    public function __construct() {
        $this->instructions = [];
    }

    public function addInstruction($type, $param = [], $callerInfo = null) {
        if ($callerInfo == null) {
            $callerInfo = cdbg::callerInfo();
        }
        if ($type instanceof CReport_Jasper_Instruction) {
            $instruction = $type;
        } else {
            $instruction = new CReport_Jasper_Instruction($type, $param, $callerInfo);
        }
        $this->instructions[] = $instruction;
        // $generator = CReport_Jasper_Manager::instance()->getGenerator();
        // if ($generator) {
        //     $this->run($generator->getReport()->getProcessor());
        // }

        return $this;
    }

    public function run(CReport_Jasper_ProcessorAbstract $processor) {
        $instructions = $this->instructions;
        $this->instructions = [];
        foreach ($instructions as $instruction) {
            $instruction->run($processor);
        }
    }

    /**
     * @return CReport_Jasper_Instruction[]
     */
    public function all() {
        return $this->instructions;
    }
}
