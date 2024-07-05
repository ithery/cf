<?php

final class CReport_Jasper_Instructions {
    /**
     * @var null|TCPDF
     */
    public static $objOutPut;

    public static $fontdir;

    public static $JasperObj;

    public static $currrentPage = 1;

    public static $yAxis;

    public static $arrayPageSetting;

    public static $print_expression_result;

    public static $lastPageFooter = true;

    public static $processingPageFooter = false;

    private static $intructions = [];

    private static $instructionProcessor = 'CReport_Jasper_Processor_PdfProcessor';

    private function __construct() {
    }

    public static function setProcessor($instructionProcessor) {
        self::$instructionProcessor = $instructionProcessor;
    }

    public static function prepare($report) {
        self::$instructionProcessor::prepare($report);
    }

    public static function addInstruction($instruction) {
        self::$intructions[] = $instruction;
    }

    public static function setJasperObj(CReport_Jasper_Element $JasperObj) {
        self::$JasperObj = $JasperObj;
    }

    public static function get() {
        return self::$objOutPut;
    }

    public static function getInstructions() {
        return self::$intructions;
    }

    public static function clearInstructrions() {
        self::$intructions = [];
    }

    public static function getPageNo() {
        return self::$objOutPut->PageNo();
    }

    public static function runInstructions() {
        $pdf = self::$objOutPut;
        $JasperObj = self::$JasperObj;
        $instructions = self::$intructions;
        //var_dump($instructions);
        self::$intructions = [];
        //$maxheight = null;
        foreach ($instructions as $arraydata) {
            $methodName = $arraydata['type'];
            $methodName = $methodName == 'break' ? 'breaker' : $methodName;

            //$instructionProcessorClass = '\JasperPHP\/' + ;
            $instruction = new self::$instructionProcessor($JasperObj);
            if (method_exists($instruction, $methodName)) {
                $instruction->$methodName($arraydata);
            }
        }
    }
}
