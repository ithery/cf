<?php

class Controller_Demo_Report_MasterDetail extends \Cresenity\Demo\Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $app = c::app();
        $app->title('Report - Master Detail');
        $app->addDiv()->addClass('mb-2')
            ->addAction()->setLabel('Download Excel')->addClass('btn btn-sm btn-outline-success')
            ->setLink(c::url('demo/report/masterDetail/excel'));
        $app->addIframe()->setSrc(c::url('demo/report/masterDetail/pdf'))
            ->customCss('width', '100%')
            ->customCss('height', '800px');

        return $app;
    }

    public function pdf() {
        return $this->buildReport()->downloadPdf();
    }

    public function excel() {
        return $this->buildReport()->downloadExcel('master-detail.xlsx');
    }

    /**
     * @return CReport_Builder
     */
    private function buildReport() {
        $report = CReport::builder();
        $xml = c::view('demo.page.report.master-detail-jrxml')->render();
        $report->fromXml($xml);
        $report->setDataFromCollection($this->buildRows());

        return $report;
    }

    /**
     * @return CCollection
     */
    private function buildRows() {
        $salesList = Cresenity\Demo\Model\Sales::query()->orderBy('sales_id')->get()->take(8);
        $detailList = Cresenity\Demo\Model\SalesDetail::query()->get();
        $customerList = Cresenity\Demo\Model\Customer::query()->get()->keyBy('customer_id');
        $productList = Cresenity\Demo\Model\Product::query()->get()->keyBy('product_id');

        $rows = c::collect();
        foreach ($salesList as $sales) {
            $customer = $customerList->get($sales->customer_id);
            foreach ($detailList->where('sales_id', $sales->sales_id) as $detail) {
                $product = $productList->get($detail->product_id);
                $rows->push([
                    'invoice_no' => $sales->invoice_no,
                    'sales_date' => $sales->sales_date,
                    'customer_name' => $customer ? $customer->name : '-',
                    'product_name' => $product ? $product->name : '-',
                    'qty' => $detail->qty,
                    'price' => $detail->price,
                    'subtotal' => $detail->subtotal,
                ]);
            }
        }

        return $rows;
    }
}
