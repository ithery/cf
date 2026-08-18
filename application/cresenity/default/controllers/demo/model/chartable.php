<?php

class Controller_Demo_Model_Chartable extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Chartable Trait');

        $app->addDiv()->addClass('mb-3')->add(
            'This demo uses <code>CModel_Chartable_ChartableTrait</code> to generate chart data directly from model queries. '
            . 'Methods like <code>countForGroup()</code>, <code>countByMonths()</code>, and <code>sumForGroup()</code> '
            . 'generate optimized SQL with database-agnostic date expressions (MySQL, PostgreSQL, SQLite, SQL Server).'
        );

        $row = $app->addDiv()->addClass('row');

        $this->salesByStatus($row);
        $this->salesByMonth($row);
        $this->revenueByMonth($row);
        $this->topCustomers($row);
        $this->topProducts($row);
        $this->salesByCity($row);

        return $app;
    }

    /**
     * @param CElement $row
     */
    private function salesByStatus($row) {
        $widget = $row->addDiv()->addClass('col-md-4')->addWidget()->setTitle('Sales by Status');

        $groupData = \Cresenity\Demo\Model\Sales::countForGroup('status');

        $pieChart = CChart::pieChart();
        $pieChart->addSeries($groupData->pluck('value')->toArray());
        $pieChart->setDataLabels($groupData->pluck('label')->map(function ($s) {
            return ucfirst($s);
        })->toArray());
        $pieChart->setColors(['#86efac', '#fcd34d', '#fca5a5']);
        $widget->addChart()->setChart($pieChart);
    }

    /**
     * @param CElement $row
     */
    private function salesByMonth($row) {
        $widget = $row->addDiv()->addClass('col-md-4')->addWidget()->setTitle('Sales Count by Month');

        $monthData = \Cresenity\Demo\Model\Sales::countByMonths('2024-01-01', '2024-12-31', 'sales_date');

        $lineChart = CChart::lineChart();
        $lineChart->addSeries($monthData->pluck('value')->toArray(), 'Sales');
        $lineChart->setDataLabels($monthData->pluck('label')->map(function ($ym) {
            return date('M', mktime(0, 0, 0, (int) substr($ym, 4, 2), 1, (int) substr($ym, 0, 4)));
        })->toArray());
        $lineChart->setColors(['#93c5fd']);
        $widget->addChart()->setChart($lineChart);
    }

    /**
     * @param CElement $row
     */
    private function revenueByMonth($row) {
        $widget = $row->addDiv()->addClass('col-md-4')->addWidget()->setTitle('Revenue by Month');

        $revenueData = \Cresenity\Demo\Model\Sales::sumByMonths('total', '2024-01-01', '2024-12-31', 'sales_date');

        $barChart = CChart::barChart();
        $barChart->addSeries($revenueData->pluck('value')->toArray(), 'Revenue');
        $barChart->setDataLabels($revenueData->pluck('label')->map(function ($ym) {
            return date('M', mktime(0, 0, 0, (int) substr($ym, 4, 2), 1, (int) substr($ym, 0, 4)));
        })->toArray());
        $barChart->setColors(['#a5b4fc']);
        $widget->addChart()->setChart($barChart);
    }

    /**
     * @param CElement $row
     */
    private function topCustomers($row) {
        $widget = $row->addDiv()->addClass('col-md-4')->addWidget()->setTitle('Top Customers by Orders');

        $customerData = \Cresenity\Demo\Model\Sales::countForGroup('customer_id');
        $top10 = $customerData->take(10);

        $labels = $top10->pluck('label')->map(function ($customerId) {
            $customer = \Cresenity\Demo\Model\Customer::find($customerId);

            return $customer ? $customer->name : 'Unknown';
        })->toArray();

        $barChart = CChart::barChart();
        $barChart->addSeries($top10->pluck('value')->toArray(), 'Orders');
        $barChart->setDataLabels($labels);
        $barChart->setColors(['#f9a8d4']);
        $widget->addChart()->setChart($barChart);
    }

    /**
     * @param CElement $row
     */
    private function topProducts($row) {
        $widget = $row->addDiv()->addClass('col-md-4')->addWidget()->setTitle('Top Products by Qty Sold');

        $productData = \Cresenity\Demo\Model\SalesDetail::sumForGroup('product_id', 'qty');
        $top10 = $productData->take(10);

        $labels = $top10->pluck('label')->map(function ($productId) {
            $product = \Cresenity\Demo\Model\Product::find($productId);

            return $product ? $product->name : 'Unknown';
        })->toArray();

        $barChart = CChart::barChart();
        $barChart->addSeries($top10->pluck('value')->toArray(), 'Qty Sold');
        $barChart->setDataLabels($labels);
        $barChart->setColors(['#fdba74']);
        $widget->addChart()->setChart($barChart);
    }

    /**
     * @param CElement $row
     */
    private function salesByCity($row) {
        $widget = $row->addDiv()->addClass('col-md-4')->addWidget()->setTitle('Sales by City');

        $sales = \Cresenity\Demo\Model\Sales::all();
        $cityData = $sales->groupBy(function ($sale) {
            $customer = \Cresenity\Demo\Model\Customer::find($sale->customer_id);

            return $customer ? $customer->city : 'Unknown';
        })->map->count()->sortDesc()->take(10);

        $pieChart = CChart::pieChart();
        $pieChart->addSeries($cityData->values()->toArray());
        $pieChart->setDataLabels($cityData->keys()->toArray());
        $pieChart->setColors(['#fca5a5', '#fdba74', '#fcd34d', '#86efac', '#6ee7b7', '#67e8f9', '#93c5fd', '#a5b4fc', '#d8b4fe', '#f9a8d4']);
        $widget->addChart()->setChart($pieChart);
    }
}
