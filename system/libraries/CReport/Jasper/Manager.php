<?php

class CReport_Jasper_Manager {
    /**
     * @var CReport_Jasper_Report_Generator
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

    public function setGenerator(CReport_Jasper_Report_Generator $generator) {
        $this->generator = $generator;

        return $this;
    }

    /**
     * @return null|CReport_Jasper_Report_Generator
     */
    public function getGenerator() {
        return $this->generator;
    }

    public function unsetGenerator() {
        $this->generator = null;

        return $this;
    }
}
