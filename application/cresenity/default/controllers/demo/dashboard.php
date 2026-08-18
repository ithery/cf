<?php
class ConfigTransformer {

    /**
     * @var CCollection
     */
    protected $items;

    public function __construct() {
        $this->items = c::collect();
    }

    public function transform($keys, $alias = '') {
        return c::collect($keys)->map(function ($keys, $index) use ($alias) {
            if ($alias) {
                $alias = $alias . '.';
            }
            if (!is_string($index)) {
                return;
            }
            $alias .= $index;
            if (is_array($keys)) {
                return $this->transform($keys, $alias);
            } else {
                $this->items->push($alias);

                return $keys;
            }
        });
    }

    public function all(): array {
        return $this->items->filter(function ($config, $key) {
            return strpos($config, 'app.providers') === false
                && strpos($config, 'filesystems.links') === false
                && strpos($config, 'app.aliases') === false;
        })->toArray();
    }
}

class Controller_Demo_Dashboard extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Dashboard');

        $sales = \Cresenity\Demo\Model\Sales::all();
        $customers = \Cresenity\Demo\Model\Customer::all();
        $products = \Cresenity\Demo\Model\Product::all();
        $details = \Cresenity\Demo\Model\SalesDetail::all();

        $this->buildStatCards($app, $sales, $customers, $products);
        $this->buildChartRow($app, $sales);
        $this->buildBottomRow($app, $sales, $details);

        return $app;
    }

    /**
     * @param CApp        $app
     * @param CCollection $sales
     * @param CCollection $customers
     * @param CCollection $products
     */
    private function buildStatCards($app, $sales, $customers, $products) {
        $row = $app->addDiv()->addClass('row mb-3');

        $totalSales = $sales->count();
        $totalRevenue = $sales->where('status', 'paid')->sum('total');
        $totalCustomers = $customers->count();
        $totalProducts = $products->count();

        $stats = [
            ['label' => 'Total Sales', 'value' => number_format($totalSales), 'icon' => 'ti-receipt', 'color' => '#6366f1', 'sub' => $sales->where('status', 'paid')->count() . ' paid'],
            ['label' => 'Revenue', 'value' => 'Rp ' . number_format($totalRevenue / 1000000, 1) . 'M', 'icon' => 'ti-money', 'color' => '#10b981', 'sub' => $sales->where('status', 'pending')->count() . ' pending'],
            ['label' => 'Customers', 'value' => number_format($totalCustomers), 'icon' => 'ti-user', 'color' => '#f59e0b', 'sub' => $customers->where('status', 'active')->count() . ' active'],
            ['label' => 'Products', 'value' => number_format($totalProducts), 'icon' => 'ti-package', 'color' => '#cc131f', 'sub' => number_format($products->sum('stock')) . ' in stock'],
        ];

        foreach ($stats as $stat) {
            $col = $row->addDiv()->addClass('col-md-3');
            $widget = $col->addWidget();
            $widget->header()->setVisibility(false);
            $widget->setNoPadding(true);
            $inner = $widget->addDiv()->addClass('d-flex align-items-center p-3');
            $iconDiv = $inner->addDiv()->setAttr('style', 'width:48px;height:48px;border-radius:12px;background:' . $stat['color'] . '15;display:flex;align-items:center;justify-content:center;margin-right:16px;');
            $iconDiv->addIcon()->setIcon($stat['icon'])->setAttr('style', 'color:' . $stat['color'] . ';font-size:1.25rem;');
            $textDiv = $inner->addDiv();
            $textDiv->addDiv()->setAttr('style', 'font-size:0.75rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;')->add($stat['label']);
            $textDiv->addDiv()->setAttr('style', 'font-size:1.5rem;font-weight:700;color:#1e293b;line-height:1.2;')->add($stat['value']);
            $textDiv->addDiv()->setAttr('style', 'font-size:0.75rem;color:#10b981;margin-top:2px;')->add($stat['sub']);
        }
    }

    /**
     * @param CApp        $app
     * @param CCollection $sales
     */
    private function buildChartRow($app, $sales) {
        $row = $app->addDiv()->addClass('row mb-3');

        // Revenue trend line chart
        $widget = $row->addDiv()->addClass('col-md-8')->addWidget()->setTitle('Revenue Trend (2024)');
        $monthData = $sales->where('status', 'paid')->groupBy(function ($s) {
            return substr($s->sales_date, 0, 7);
        })->map->sum('total')->sortKeys();

        $lineChart = CChart::lineChart();
        $lineChart->addSeries($monthData->values()->toArray(), 'Revenue');
        $pendingData = $sales->where('status', 'pending')->groupBy(function ($s) {
            return substr($s->sales_date, 0, 7);
        })->map->sum('total')->sortKeys();
        $lineChart->addSeries($pendingData->values()->toArray(), 'Pending');
        $lineChart->setDataLabels($monthData->keys()->map(function ($ym) {
            return date('M', mktime(0, 0, 0, (int) substr($ym, 5, 2), 1));
        })->toArray());
        $lineChart->setColors(['#6366f1', '#fcd34d']);
        $lineChart->showLegend();
        $widget->addChart()->setChart($lineChart);

        // Sales by status pie
        $widget = $row->addDiv()->addClass('col-md-4')->addWidget()->setTitle('Sales by Status');
        $statusData = $sales->groupBy('status')->map->count();

        $pieChart = CChart::pieChart();
        $pieChart->addSeries($statusData->values()->toArray());
        $pieChart->setDataLabels($statusData->keys()->map(function ($s) {
            return ucfirst($s);
        })->toArray());
        $pieChart->setColors(['#10b981', '#fcd34d', '#ef4444']);
        $widget->addChart()->setChart($pieChart);
    }

    /**
     * @param CApp        $app
     * @param CCollection $sales
     * @param CCollection $details
     */
    private function buildBottomRow($app, $sales, $details) {
        $row = $app->addDiv()->addClass('row');

        // Recent sales
        $widget = $row->addDiv()->addClass('col-md-4')->addWidget()->setTitle('Recent Sales');
        $widget->setNoPadding(true);
        $recent = $sales->sortByDesc('sales_date')->take(8);
        $listDiv = $widget->addDiv();
        foreach ($recent as $sale) {
            $customer = \Cresenity\Demo\Model\Customer::find($sale->customer_id);
            $item = $listDiv->addDiv()->setAttr('style', 'display:flex;justify-content:space-between;align-items:center;padding:10px 16px;border-bottom:1px solid #f1f5f9;');
            $left = $item->addDiv();
            $left->addDiv()->setAttr('style', 'font-weight:500;font-size:0.85rem;color:#1e293b;')->add($sale->invoice_no);
            $left->addDiv()->setAttr('style', 'font-size:0.75rem;color:#94a3b8;')->add(($customer ? $customer->name : 'Unknown') . ' · ' . $sale->sales_date);
            $right = $item->addDiv()->setAttr('style', 'text-align:right;');
            $right->addDiv()->setAttr('style', 'font-weight:600;font-size:0.85rem;color:#1e293b;')->add('Rp ' . number_format($sale->total));
            $badgeColor = $sale->status === 'paid' ? '#dcfce7;color:#166534' : ($sale->status === 'pending' ? '#fef9c3;color:#854d0e' : '#fef2f2;color:#991b1b');
            $right->addSpan()->setAttr('style', 'font-size:0.65rem;padding:2px 8px;border-radius:10px;background:' . $badgeColor . ';')->add(ucfirst($sale->status));
        }

        // Top customers
        $widget = $row->addDiv()->addClass('col-md-4')->addWidget()->setTitle('Top Customers');
        $widget->setNoPadding(true);
        $customerSales = $sales->where('status', 'paid')->groupBy('customer_id')->map(function ($items) {
            return ['count' => $items->count(), 'total' => $items->sum('total')];
        })->sortByDesc('total')->take(8);

        $listDiv = $widget->addDiv();
        $rank = 1;
        foreach ($customerSales as $customerId => $data) {
            $customer = \Cresenity\Demo\Model\Customer::find($customerId);
            $item = $listDiv->addDiv()->setAttr('style', 'display:flex;justify-content:space-between;align-items:center;padding:10px 16px;border-bottom:1px solid #f1f5f9;');
            $left = $item->addDiv()->setAttr('style', 'display:flex;align-items:center;');
            $left->addSpan()->setAttr('style', 'width:24px;height:24px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:600;color:#64748b;margin-right:12px;')->add($rank);
            $nameDiv = $left->addDiv();
            $nameDiv->addDiv()->setAttr('style', 'font-weight:500;font-size:0.85rem;color:#1e293b;')->add($customer ? $customer->name : 'Unknown');
            $nameDiv->addDiv()->setAttr('style', 'font-size:0.75rem;color:#94a3b8;')->add($data['count'] . ' orders');
            $item->addDiv()->setAttr('style', 'font-weight:600;font-size:0.85rem;color:#1e293b;')->add('Rp ' . number_format($data['total']));
            $rank++;
        }

        // Top products
        $widget = $row->addDiv()->addClass('col-md-4')->addWidget()->setTitle('Top Products');
        $widget->setNoPadding(true);
        $productQty = $details->groupBy('product_id')->map(function ($items) {
            return ['qty' => $items->sum('qty'), 'revenue' => $items->sum('subtotal')];
        })->sortByDesc('revenue')->take(8);

        $listDiv = $widget->addDiv();
        $rank = 1;
        foreach ($productQty as $productId => $data) {
            $product = \Cresenity\Demo\Model\Product::find($productId);
            $item = $listDiv->addDiv()->setAttr('style', 'display:flex;justify-content:space-between;align-items:center;padding:10px 16px;border-bottom:1px solid #f1f5f9;');
            $left = $item->addDiv()->setAttr('style', 'display:flex;align-items:center;');
            $left->addSpan()->setAttr('style', 'width:24px;height:24px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:600;color:#64748b;margin-right:12px;')->add($rank);
            $nameDiv = $left->addDiv();
            $nameDiv->addDiv()->setAttr('style', 'font-weight:500;font-size:0.85rem;color:#1e293b;')->add($product ? $product->name : 'Unknown');
            $nameDiv->addDiv()->setAttr('style', 'font-size:0.75rem;color:#94a3b8;')->add($data['qty'] . ' sold');
            $item->addDiv()->setAttr('style', 'font-weight:600;font-size:0.85rem;color:#1e293b;')->add('Rp ' . number_format($data['revenue']));
            $rank++;
        }
    }

    public function config() {
        $configs = CConfig::repository()->all();
        $config = new ConfigTransformer();
        $config->transform($configs);
        echo json_encode($config->all());
    }

    public function translation() {
        $translations = [];
        $paths = array_reverse(CF::paths());
        foreach ($paths as $path) {
            $translationPath = $path . 'i18n' . DIRECTORY_SEPARATOR;
            if (CFile::isDirectory($translationPath)) {
                $directories = CFile::directories($translationPath);
                foreach ($directories as $directory) {
                    $files = CFile::files($directory);
                    foreach ($files as $file) {
                        $fileName = str_replace('.php', '', $file->getFileName());
                        $fields = include $file->getPathName();
                        if (is_array($fields)) {
                            foreach ($fields as $field => $message) {
                                $translations[] = "{$fileName}.{$field}";
                                if ($fileName == 'core') {
                                    $translations[] = $field;
                                }
                            }
                        }
                    }
                }
            }
        }
        echo json_encode(array_filter($translations));
    }

    public function getPermissions($navs) {
        $permissions = [];
        foreach ($navs as $nav) {
            $name = carr::get($nav, 'name');
            $subnav = carr::get($nav, 'subnav');
            $action = carr::get($nav, 'action');
            if ($name) {
                $permissions[] = $name;
            }
            if (is_array($subnav)) {
                $subnavPermissions = $this->getPermissions($subnav);
                $permissions = array_merge($permissions, $subnavPermissions);
            }
            if (is_array($action)) {
                foreach ($action as $act) {
                    $actName = carr::get($act, 'name');
                    if ($actName) {
                        $permissions[] = $actName;
                    }
                }
            }
        }

        return $permissions;
    }

    public function permission() {
        $permissions = [];
        $path = CF::appDir();
        $navPath = $path . DS . 'default' . DS . 'navs';
        $files = CFile::files($navPath);
        foreach ($files as $file) {
            // $fileName = str_replace('.php', '', $file->getFileName());
            $navs = include $file->getPathName();
            if (is_array($navs)) {
                $permissions = array_merge($permissions, $this->getPermissions($navs));
            }
        }
        echo json_encode(array_filter($permissions));
    }
}
