<?php

    defined('SYSPATH') or die('No direct access allowed.');
    /**
     * PHP Excel library. Helper class to make spreadsheet creation easier.
     *
     * @package    Spreadsheet
     * @author     Flynsarmy
     * @website    http://www.flynsarmy.com/
     * @license    TEH FREEZ
     */
    require_once dirname(__FILE__) . "/Lib/phpexcel/PHPExcel.php";
    require_once dirname(__FILE__) . "/Lib/phpexcel/PHPExcel/IOFactory.php";

    class CExcel {

        private $phpexcel;

        /*
         * Purpose: Creates the spreadsheet with given or default settings
         * Input: array $headers with optional parameters: title, subject, description, author
         * Returns: void
         */

        public function __construct($headers = array()) {
            $headers = array_merge(array(
                'title' => 'New Spreadsheet',
                'subject' => 'New Spreadsheet',
                'description' => 'New Spreadsheet',
                'author' => 'Cresenity',
                    ), $headers);

            $this->phpexcel = new PHPExcel();
            // Set properties
            $this->phpexcel->getProperties()
                    ->setCreator($headers['author'])
                    ->setTitle($headers['title'])
                    ->setSubject($headers['subject'])
                    ->setDescription($headers['description']);
            //->setActiveSheetIndex(0);
            //$this->phpexcel->getActiveSheet()->setTitle('Minimalistic demo');
        }

        public static function factory($headers = array()) {
            $s = new CExcel($headers);
            return $s;
        }

        public static function num2alpha($n) {
            for ($r = ""; $n >= 0; $n = intval($n / 26) - 1)
                $r = chr($n % 26 + 0x41) . $r;
            return $r;
        }

        public function garbage_collect() {
            $this->phpexcel->garbageCollect();
            return $this;
        }

        public function set_creator($creator) {
            $this->phpexcel->getProperties()->setCreator($creator);
            return $this;
        }

        public function set_title($title) {
            $this->phpexcel->getProperties()->setTitle($title);
            return $this;
        }

        public function set_subject($subject) {
            $this->phpexcel->getProperties()->setSubject($subject);
            return $this;
        }

        public function set_description($description) {
            $this->phpexcel->getProperties()->setDescription($description);
            return $this;
        }

        public function phpexcel() {
            return $this->phpexcel;
        }

        public function get_active_sheet() {
            return $this->phpexcel->getActiveSheet();
        }

        public function set_active_sheet_name($name) {
            $this->phpexcel->getActiveSheet()->setTitle($name);
            return $this;
        }
        
        public function get_highest_row(){
            return $this->phpexcel->getActiveSheet()->getHighestRow();
        }

        public function get_active_sheet_name() {
            return $this->phpexcel->getActiveSheet()->getTitle();
        }

        function set_auto_width($col_start = "", $col_end = "") {
            $sheet = $this->phpexcel->getActiveSheet();
            if (empty($col_end)) {//not defined the last column, set it the max one
                $col_end = $sheet->getColumnDimension($sheet->getHighestColumn())->getColumnIndex();
            }
            if ($col_start == "") $col_start = "A";
            if (is_numeric($col_start))
                    $col_start = $this->num2alpha($col_start);

            $col_end++;
            for ($i = $col_start; $i !== $col_end; $i++) {
                $sheet->getColumnDimension($i)->setAutoSize(true);
            }
            $sheet->calculateColumnWidths();
        }

        public function set_align_by_index($col, $row, $align) {
            $sheet = $this->phpexcel->getActiveSheet();
            if (is_numeric($col)) $col = $this->num2alpha($col);

            if ($align == "left") {
                $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            }
            if ($align == "right") {
                $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            }
            if ($align == "center") {
                $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
        }

        public function merge_cell($col1, $row1, $col2, $row2) {
            $sheet = $this->phpexcel->getActiveSheet();
            if (is_numeric($col1)) $col1 = $this->num2alpha($col1);
            if (is_numeric($col2)) $col2 = $this->num2alpha($col2);
            $sheet->mergeCells($col1 . $row1 . ":" . $col2 . $row2);
        }

        public function set_header_style($row = null, $style = array(), $col_start = "", $col_end = "") {
            if ($row == null) $row = '1';
            $sheet = $this->phpexcel->getActiveSheet();
            if (empty($style)) {
                $style = array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '333333'),
                    ),
                    'font' => array(
                        'bold' => true,
                        'color' => array('rgb' => 'FFFFFF'),
                    ),
                );
            }
            if (empty($col_end)) {//not defined the last column, set it the max one
                $col_end = $sheet->getColumnDimension($sheet->getHighestColumn())->getColumnIndex();
            }
            if ($col_start == "") $col_start = "A";
            if (is_numeric($col_start))
                    $col_start = $this->num2alpha($col_start);
            if (is_numeric($col_end)) $col_end = $this->num2alpha($col_end);

            $sheet->getStyle($col_start . $row . ':' . $col_end . $row)->applyFromArray($style);
        }

        public function set_row_style($row, $style = array(), $col_start = "", $col_end = "") {
            $sheet = $this->phpexcel->getActiveSheet();
            if (empty($style)) {
                $style = array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '333333'),
                    ),
                    'font' => array(
                        'bold' => true,
                        'color' => array('rgb' => 'FFFFFF'),
                    ),
                );
            }
            if (empty($col_end)) {//not defined the last column, set it the max one
                $col_end = $sheet->getColumnDimension($sheet->getHighestColumn())->getColumnIndex();
            }
            if ($col_start == "") $col_start = "A";
            if (is_numeric($col_start))
                    $col_start = $this->num2alpha($col_start);
            if (is_numeric($col_end)) $col_end = $this->num2alpha($col_end);

            $sheet->getStyle($col_start . $row . ':' . $col_end . $row)->applyFromArray($style);
        }

        public function add_sheet() {
            return $this->phpexcel->addSheet();
        }

        /*
         * Purpose Writes cells to the spreadsheet
         * Input: array of array( [row] => array([col]=>[value]) ) ie $arr[row][col] => value
         * Returns: void
         */

        public function write_cell($cell, $value) {

            $this->phpexcel->getActiveSheet()->setCellValue($cell, $value);
            return $this;
        }

        public function write_by_index($column, $row, $value) {

            $this->phpexcel->getActiveSheet()->setCellValueByColumnAndRow($column, $row, $value);
            //->setCellValueExplicit('A1', '0029', PHPExcel_Cell_DataType::TYPE_STRING);
            return $this;
        }

        public function read_cell($cell) {

            return $this->phpexcel->getActiveSheet()->getCell($cell)->getCalculatedValue();
        }

        public function read_by_index($column, $row) {

            return $this->phpexcel->getActiveSheet()->getCellByColumnAndRow($column, $row)->getCalculatedValue();
        }

        public function set_list_validation_by_index($col, $row, $source) {
            $cell = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
            $this->set_list_validation($cell, $source);
        }

        public function set_column_width($index, $width) {
            $sheet = $this->phpexcel->getActiveSheet();
            $col = $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($index));
            $col->setWidth($width);
        }

        public function set_list_validation($cell, $source) {

            $objValidation = $this->phpexcel->getActiveSheet()->getCell($cell)->getDataValidation();
            $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
            $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
            $objValidation->setAllowBlank(false);
            $objValidation->setShowInputMessage(true);
            $objValidation->setShowErrorMessage(true);
            $objValidation->setShowDropDown(true);
            $objValidation->setErrorTitle('Input error');
            $objValidation->setError('Value is not in list.');
            $objValidation->setPromptTitle('Pick from list');
            $objValidation->setPrompt('Please pick a value from the drop-down list.');
            $objValidation->setFormula1($source);
            $objValidation->setFormula2($source);
        }

        public function setData(array $data, $multi_sheet = false) {
            if (empty($this->phpexcel)) $this->create();

            //Single sheet ones can just dump everything to the current sheet
            if (!$multi_sheet) {
                $Sheet = $this->phpexcel->getActiveSheet();
                $this->setSheetData($data, $Sheet);
            }
            //Hvae to do a little more work with multi-sheet
            else {
                foreach ($data as $sheetName => $sheetData) {
                    $Sheet = $this->phpexcel->createSheet();
                    $Sheet->setTitle($sheetName);
                    $this->setSheetData($sheetData, $Sheet);
                }
                //Now remove the auto-created blank sheet at start of XLS
                $this->phpexcel->removeSheetByIndex(0);
            }

            /*
              array(
              1 => array('A1', 'B1', 'C1', 'D1', 'E1')
              2 => array('A2', 'B2', 'C2', 'D2', 'E2')
              3 => array('A3', 'B3', 'C3', 'D3', 'E3')
              );
             */
        }

        public function setSheetData(array $data, PHPExcel_Worksheet $Sheet) {
            foreach ($data as $row => $columns)
                foreach ($columns as $column => $value)
                    $Sheet->setCellValueByColumnAndRow($column, $row, $value);
        }

        /*
         * Purpose: Writes spreadsheet to file
         * Input: array $settings with optional parameters: format, path, name (no extension)
         * Returns: Path to spreadsheet
         */

        public function save($name) {
            if (empty($this->phpexcel)) $this->create();

            $Writer = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
            // If you want to output e.g. a PDF file, simply do:
            //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
            $Writer->save($name);

            return $name;
        }

        public function savepdf($name) {
            if (empty($this->phpexcel)) $this->create();

            $Writer = PHPExcel_IOFactory::createWriter($this->phpexcel, 'PDF');
            // If you want to output e.g. a PDF file, simply do:
            //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
            $Writer->save($name);

            return $name;
        }

        public function load($name) {
            /** Load $inputFileName to a PHPExcel Object  * */
            $this->phpexcel = PHPExcel_IOFactory::load($name);
            return $this;
        }
        
        public function set_read_data_only($read_data){
            $this->phpexcel->setReadDataOnly($read_data);
            return $this;
        }
 
        public static function column_index($column_char) {
            $colIndex = PHPExcel_Cell::columnIndexFromString($column_char);
        }

    }
    