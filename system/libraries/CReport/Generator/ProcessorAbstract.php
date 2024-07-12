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
    abstract public function cellHeight(array $options);

    /**
     * @param array $options
     *
     * @return void
     */
    abstract public function image(array $options);

    /**
     * @param array $options
     *
     * @return void
     */
    abstract public function line(array $options);

    /**
     * @param float $height
     *
     * @return float
     */
    abstract public function addY($height);

    /**
     * @return float
     */
    abstract public function resetY();

    /**
     * @param float $y
     *
     * @return float
     */
    abstract public function setY($y);

    /**
     * @return mixed
     */
    abstract public function getOutput();

    /**
     * @param CReport_Generator $generator
     * @param float             $height
     *
     * @return float
     */
    abstract public function preventYOverflow(CReport_Generator $generator, $height);
}
