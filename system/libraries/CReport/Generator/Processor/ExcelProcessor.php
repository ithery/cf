<?php

class CReport_Generator_Processor_ExcelProcessor extends CReport_Generator_ProcessorAbstract {
    /**
     * @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    protected $worksheet;

    /**
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    protected $spreadsheet;

    private $rowHeightPixel;

    private $columnWidthPixel;

    private $currentRow;

    public function __construct(CReport_Builder_Report $report) {
        parent::__construct($report);
        $this->spreadsheet = new CReport_Adapter_Excel_PhpSpreadsheet();
        $this->worksheet = $this->spreadsheet->getActiveSheet();
        $this->rowHeightPixel = 18;
        $this->columnWidthPixel = 50;
        $this->currentRow = 1;
    }

    public function setText($x, $y, $txt, $align = '', $pattern = '') {
        $myformat = '';

        try {
            if (strpos($pattern, '.') !== false || strpos($pattern, '#') !== false) {
                $this->worksheet->getCellByColumnAndRow($x, $y)->setValueExplicit($txt, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                $this->worksheet->getStyleByColumnAndRow($x, $y)->getNumberFormat()->setFormatCode($pattern);
            } else {
                $this->worksheet->getCellByColumnAndRow($x, $y)->setValueExplicit($txt, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }
            /* if(strpos($pattern,".")!==false || strpos($pattern,"#")!==false){

              }
              else
              $this->ws->getStyleByColumnAndRow($x, $y)->getNumberFormat()->setFormatCode('@');
             */
            //$newstrken=($this->ws->getCellByColumnAndRow($x, $y)->getValue());
            //if($this->left($txt,1)=='0' && $stlen>$newstrken){
            // for($kkk=0;$kkk<$stlen;$kkk++){
            //$myformat.="0";
            // echo $myformat.",$txt<br/>";
            //  }
            //$this->ws->getCellByColumnAndRow($x, $y)->getNumberFormat()->setFormatCode($myformat);
            //}
            //setCellValueByColumnAndRow($x,$y,$txt);

            if ($align == 'C') {
                $this->worksheet->getStyleByColumnAndRow($x, $y)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            } elseif ($align == 'R') {
                $this->worksheet->getStyleByColumnAndRow($x, $y)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            } else {
                $this->worksheet->getStyleByColumnAndRow($x, $y)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            }
        } catch (\PhpOffice\PhpSpreadsheet\Exception $ex) {
            cdbg::dd($x, $y, $txt, $ex);
        }
    }

    public function cell(array $options) {
        $text = carr::get($options, 'text');
        $width = carr::get($options, 'width');
        $height = carr::get($options, 'height');
        $font = carr::get($options, 'font');
        $backgroundColor = carr::get($options, 'backgroundColor');
        $x = carr::get($options, 'x');
        $y = carr::get($options, 'y');

        $x = intval($x / $this->columnWidthPixel) + 1;
        //if($x==0)$x=1 ;
        $y = intval(($y + $height) / $this->rowHeightPixel);

        $this->setText($x, $y + $this->currentRow, $text);
    }

    public function image(array $options) {
    }

    public function cellHeight(array $options) {
    }

    public function line(array $options) {
    }

    public function addY($height) {
        $yAddition = intval(($height) / $this->rowHeightPixel);
        $this->currentRow += $yAddition;
    }

    public function preventYOverflow(CReport_Generator $generator, $height) {
    }

    public function setY($y) {
    }

    public function resetY() {
    }

    /**
     * @return CReport_Adapter_Excel_Spreadsheet
     */
    public function getOutput() {
        return $this->spreadsheet;
    }

    public function resetTextColor() {
    }
}
