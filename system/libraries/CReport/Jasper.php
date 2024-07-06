<?php
/**
 * @see CElement_Component_DataTable
 */
class CReport_Jasper {
    protected $report;

    /**
     * @var CManager_Contract_DataProviderInterface
     */
    protected $dataProvider;

    public function __construct($jrxml, $param = []) {
        $this->report = new CReport_Jasper_Report($jrxml, $param);
    }

    /**
     * @return \TCPDF
     */
    public function getPdf() {
        if ($this->dataProvider) {
            $data = $this->dataProvider->toEnumerable();
            $this->report->setData($data);
        }
        CReport_Jasper_Instructions::prepare($this->report);
        $this->report->generate();
        $this->report->out();

        $pdf = CReport_Jasper_Instructions::get();

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
}
