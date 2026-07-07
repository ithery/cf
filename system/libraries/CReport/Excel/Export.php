<?php

/**
 * CExporter export wrapping a CReport_Excel_Grid, so a report can be
 * downloaded/stored/queued through the CExporter pipeline.
 *
 * <code>
 * $export = CReport::builder()->fromXml($xml)->setDataFromDataset('sales')->getExcelExport();
 * return CExporter::download($export, 'report.xlsx');
 * </code>
 */
class CReport_Excel_Export extends CExporter_Exportable implements CExporter_Concern_FromArray {
    /**
     * @var CReport_Excel_Grid
     */
    protected $grid;

    /**
     * @param CReport_Excel_Grid $grid
     */
    public function __construct(CReport_Excel_Grid $grid) {
        $this->grid = $grid;
        $this->afterSheet(function (CExporter_Event_AfterSheet $event) {
            $writer = new CReport_Excel_GridWriter($this->grid);
            $writer->write($event->sheet->getDelegate());
        });
    }

    /**
     * @return CReport_Excel_Grid
     */
    public function getGrid() {
        return $this->grid;
    }

    /**
     * The sheet is populated entirely from the grid on AfterSheet, so no rows here.
     *
     * @return array
     */
    public function getArray() {
        return [];
    }
}
