<?php

class Controller_Demo_Report_Ui extends \Cresenity\Demo\Controller {
    use CReport_UIBuilder_Trait_UIBuilderTrait;

    public function __construct() {
        parent::__construct();
        $this->registerDatasets();
    }

    public function index() {
        return $this->ui([
            'datasets' => CReport_DatasetRegistry::describe(),
            'previewUrl' => c::url('demo/report/ui/preview'),
        ]);
    }

    public function preview() {
        $jrxml = c::request()->input('jrxml');
        $dataset = c::request()->input('dataset');
        if (!$jrxml) {
            return c::response()->make('missing jrxml', 400);
        }
        $report = CReport::builder();
        $report->fromXml($jrxml);
        if ($dataset && CReport_DatasetRegistry::has($dataset)) {
            $report->setDataFromDataset($dataset);
        }
        $pdf = $report->getPdf();

        return c::response()->make($pdf->Output('preview.pdf', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="preview.pdf"',
        ]);
    }

    /**
     * @return void
     */
    private function registerDatasets() {
        CReport_DatasetRegistry::register('country', Cresenity\Demo\Model\Country::class);
        CReport_DatasetRegistry::register('product', Cresenity\Demo\Model\Product::class);
        CReport_DatasetRegistry::register('sales', function () {
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
        });
    }
}
