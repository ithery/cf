<?php

class CReport_Generator_Instruction {
    /**
     * @var Closure
     */
    protected $instruction;

    protected $page;

    protected $y;

    public function __construct($y, $page, Closure $instruction) {
        $this->y = $y;
        $this->page = $page;
        $this->instruction = $instruction;
    }

    public function run(CReport_Generator_ProcessorAbstract $processor) {
        $processor->setPage($this->page);
        $processor->setY($this->y);

        return $this->instruction->__invoke($processor);
    }
}
