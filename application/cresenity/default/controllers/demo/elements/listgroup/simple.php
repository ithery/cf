<?php

class Controller_Demo_Elements_Listgroup_Simple extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Simple List Group');
        $app->addH4()->add('Simple List Group With LI');
        $listGroup = $app->addListGroup('sales-summary');
        $liTitleCash = $listGroup->addLi()->addClass('list-group-item d-flex justify-content-between align-items-center active')->add('Sales Cash');
        $liValueCash = $listGroup->addLi()->addClass('list-group-item d-flex justify-content-between align-items-center')->add('Total Transaksi');
        $liValueCash->addSpan()->addClass('badge badge-pill')->add('Item 1');
        $liValueCash = $listGroup->addLi()->addClass('list-group-item d-flex justify-content-between align-items-center')->add('Total Topup');
        $liValueCash->addSpan()->addClass('badge badge-pill')->add('Item 2');
        $liValueCash = $listGroup->addLi()->addClass('list-group-item d-flex justify-content-between align-items-center')->add('Total Refund');
        $liValueCash->addSpan()->addClass('badge badge-pill')->add('Item 3');
        $liValueCash = $listGroup->addLi()->addClass('list-group-item d-flex justify-content-between align-items-center')->add('Total Cash');
        $liValueCash->addSpan()->addClass('badge badge-pill')->add('Item 4');

        return $app;
    }
}
