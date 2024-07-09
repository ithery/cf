<?php

abstract class CReport_Generator_ProcessorAbstract {
    protected $report;

    public function __construct(CReport_Builder_Report $report) {
        $this->report = $report;
    }

    /**
     * @param array $options
     *
     * @return void
     */
    abstract public function cell(array $options);

    /**
     * @param array $options
     *
     * @return void
     */
    abstract public function image(array $options);

    /**
     * @param float $height
     *
     * @return float
     */
    abstract public function addY($height);

    /**
     * @return mixed
     */
    abstract public function getOutput();
}
