<?php

class Controller_Demo_Elements_Table_Export extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Table Export Data');
        $isExport = (bool) c::request()->export;

        $app->addDiv()->addClass('mb-3')->add(
            'Demo for DataTable export using <code>createDownloadAction()</code>. '
            . 'Click the Export button in the widget header to download as Excel.'
        );

        $table = $app->addTable();
        $table->setDataFromModel(\Cresenity\Demo\Model\Product::class);
        $table->addColumn('sku')->setLabel('SKU')->setWidth('100');
        $table->addColumn('name')->setLabel('Product Name');
        $table->addColumn('category')->setLabel('Category')->setWidth('150');
        $table->addColumn('price')->setLabel('Price')->setCallback(function ($row, $value) {
            return 'Rp ' . number_format($value);
        })->setExportCallback(function ($row, $value) {
            return $value;
        })->setDataType('currency');
        $table->addColumn('stock')->setLabel('Stock')->setWidth('80');
        $table->setAjax(false);

        $widget = $app->addWidget()->setTitle('Product Data');
        $exportAction = $table->createDownloadAction([
            'filename' => 'products-' . date('Ymd-His') . '.xlsx',
        ]);
        $exportAction->setIcon('ti-download')->setLabel('Export Excel');
        $widget->addHeaderAction()->setIcon('ti-download')->setLabel('Export Excel')
            ->setLink($exportAction->getAttr('href'))->setLinkTarget('_blank');
        $widget->setNoPadding(true);
        $widget->add($table);

        if ($isExport) {
            return $table->downloadExcel('products-' . date('Ymd-His') . '.xlsx');
        }

        return $app;
    }
}
