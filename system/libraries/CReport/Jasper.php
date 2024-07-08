<?php
/**
 * @see CElement_Component_DataTable
 */
class CReport_Jasper {
    /**
     * @var CReport_Jasper_Report
     */
    protected $report;

    /**
     * @var CManager_Contract_DataProviderInterface
     */
    protected $dataProvider;

    public function __construct($jrxml, array $param = []) {
        $this->report = new CReport_Jasper_Report($jrxml, $param);
    }

    /**
     * @return CReport_Pdf_Adapter_TCPDF
     */
    public function getPdf() {
        if ($this->dataProvider) {
            $data = $this->dataProvider->toEnumerable();
            $this->report->setData($data);
        }

        $pdf = $this->report->getPdf();

        return $pdf;
    }

    /**
     * @return CReport_Pdf_Adapter_TCPDF
     */
    public function getSpreadsheet() {
        if ($this->dataProvider) {
            $data = $this->dataProvider->toEnumerable();
            $this->report->setData($data);
        }

        $pdf = $this->report->getPdf();

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
     * @param CCollection $arr
     *
     * @return $this
     */
    public function setDataFromCollection(CCollection $collection) {
        $this->dataProvider = CManager::createCollectionDataProvider($collection);

        return $this;
    }

    public function setDataProvider(CManager_Contract_DataProviderInterface $dataProvider) {
        $this->dataProvider = $dataProvider;

        return $this;
    }

    public static function manager() {
        return CReport_Jasper_Manager::instance();
    }
}
