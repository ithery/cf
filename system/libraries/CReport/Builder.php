<?php
/**
 * @method CReport_Builder_Element_Title        addTitle()        Add Title Element
 * @method CReport_Builder_Element_Band         addBand()         Add Band Element
 * @method CReport_Builder_Element_Image        addImage()        Add Image Element
 * @method CReport_Builder_Element_PageHeader   addPageHeader()   Add PageHeader Element
 * @method CReport_Builder_Element_Frame        addFrame()        Add Frame Element
 * @method CReport_Builder_Element_StaticText   addStaticText()   Add StaticText Element
 * @method CReport_Builder_Element_ColumnHeader addColumnHeader() Add ColumnHeader Element
 * @method CReport_Builder_Element_Detail       addDetail()       Add Detail Element
 * @method CReport_Builder_Element_TextField    addTextField()    Add TextField Element
 */
class CReport_Builder {
    use CTrait_ForwardsCalls;
    /**
     * @var CReport_Builder_Report
     */
    protected $report;

    /**
     * @var CManager_Contract_DataProviderInterface
     */
    protected $dataProvider;

    public function __construct() {
        $this->report = new CReport_Builder_Report();
    }

    public function setPaperSize($size) {
        $size = cstr::upper($size);
        $sizes = carr::get(CReport_Paper::$pageFormats, $size);

        if ($sizes == null) {
            $sizes = CReport_Paper::$pageFormats['A4'];
        }
        $this->report->setPageWidth($sizes[0]);
        $this->report->setPageHeight($sizes[1]);

        return $this;
    }

    /**
     * @param CModel|CModel_Query|string $model
     * @param null|mixed                 $queryCallback
     *
     * @return $this
     */
    public function setDataFromModel($model, $queryCallback = null) {
        $this->dataProvider = CManager::createModelDataProvider($model, $queryCallback);

        return $this;
    }

    /**
     * @param CCollection $collection
     *
     * @return $this
     */
    public function setDataFromCollection(CCollection $collection) {
        $this->dataProvider = CManager::createCollectionDataProvider($collection);

        return $this;
    }

    public function setOrientation($orientation) {
        $this->report->setOrientation(cstr::lower($orientation) == CReport_Paper::ORIENTATION_LANDSCAPE ? CReport_Paper::ORIENTATION_LANDSCAPE : CReport_Paper::ORIENTATION_PORTRAIT);
    }

    /**
     * @return CReport_Pdf_Adapter_TCPDF
     */
    public function getPdf() {
        $jrxml = $this->report->toJrXml();
        // cdbg::dd($jrxml);
        $report = CReport::jasper($jrxml, []);
        if ($this->dataProvider) {
            $report->setDataProvider($this->dataProvider);
        }

        $pdf = $report->getPdf();

        return $pdf;
    }

    /**
     * @param null|string $filename
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadPdf($filename = null) {
        if ($filename == null) {
            $filename = 'report-' . date('YmdHis') . '-' . uniqid() . '.pdf';
        }
        $pdf = $this->getPdf();

        return c::response()->streamDownload(function () use ($pdf, $filename) {
            $pdf->Output($filename, 'I');
        }, $filename);
    }

    /**
     * @param string $method
     * @param array  $parameters
     *
     * @see CModel
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        $result = $this->forwardCallTo($this->report, $method, $parameters);
        if ($result === $this->report) {
            //this is chained method
            return $this;
        }

        return $result;
    }
}
