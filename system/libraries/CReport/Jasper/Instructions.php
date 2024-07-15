<?php

final class CReport_Jasper_Instructions {
    /**
     * @var null|CReport_Adapter_Pdf_TCPDF
     */
    public static $objOutPut;

    public static $fontdir;

    public static $currentPage = 1;

    public static $yAxis;

    public static $arrayPageSetting;

    public static $print_expression_result;

    public static $lastPageFooter = true;

    public static $processingPageFooter = false;

    public static $intructions = [];

    private function __construct() {
    }

    public static function addInstruction(array $instruction) {
        $type = $instruction['type'];
        CReport_Jasper_Manager::instance()->getGenerator()->getReport()->getInstructions()->addInstruction($type, $instruction);
        //self::$intructions[] = $instruction;
    }

    public static function get() {
        return self::$objOutPut;
    }

    public static function getPageNo() {
        return self::$objOutPut->PageNo();
    }

    public static function runInstructions() {
        $report = CReport_Jasper_Manager::instance()->getGenerator()->getReport();
        $processor = $report->getProcessor();
        // cdbg::dd($report->getInstructions()->all());
        $report->getInstructions()->run($processor);
        // $instructions = self::$intructions;
        // //var_dump($instructions);
        // self::$intructions = [];
        // //$maxheight = null;
        // foreach ($instructions as $arraydata) {
        //     $methodName = $arraydata['type'];
        //     $methodName = $methodName == 'break' ? 'breaker' : $methodName;

        //     //$instructionProcessorClass = '\JasperPHP\/' + ;

        //     if (method_exists($processor, $methodName)) {
        //         $processor->$methodName($arraydata);
        //     } else {
        //         throw new Exception('Method name ' . $methodName . 'is not exists on ' . get_class($processor));
        //     }
        // }
    }
}
