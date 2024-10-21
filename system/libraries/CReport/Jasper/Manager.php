<?php

class CReport_Jasper_Manager {
    /**
     * @var CReport_Jasper_Manager_Generator
     */
    protected $generator;

    private static $instance;

    /**
     * @return CReport_Jasper_Manager
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->generator = new CReport_Jasper_Manager_Generator();
    }

    /**
     * @return null|CReport_Jasper_Manager_Generator
     */
    public function getGenerator() {
        return $this->generator;
    }

    /**
     * @return null|CReport_Jasper_ProcessorAbstract
     */
    public function getProcessor() {
        return $this->generator->getProcessor();
    }

    /**
     * @return null|CReport_Jasper_Report
     */
    public function getReport() {
        return $this->generator->getReport();
    }
}
