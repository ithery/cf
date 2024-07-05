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

    public function exportPdf() {
        if ($this->dataProvider) {
            $data = $this->dataProvider->toEnumerable();
            $this->report->setData($data);
        }
        CReport_Jasper_Instructions::prepare($this->report);
        $this->report->generate();
        $this->report->out();

        $pdf = CReport_Jasper_Instructions::get();
        // cdbg::dd($pdf);
        $pdf->Output('report.pdf', 'I');
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
