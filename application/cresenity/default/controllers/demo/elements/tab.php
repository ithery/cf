<?php

class Controller_Demo_Elements_Tab extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Tab');
        $tabList = $app->addTabList()->setTabPosition('top')->addClass('mb-3');
        $tab1 = $tabList->addTab()->setLabel('Tab 1')
            ->addDiv()->add('Tab 1');
        $tab2 = $tabList->addTab()->setLabel('Tab 2')
            ->addDiv()->add('Tab 2');

        $app->addH3()->add('Tab Ajax');
        $tabList = $app->addTabList()->setTabPosition('left')->addClass('mb-3');
        $tab1 = $tabList->addTab()->setLabel('Activity')
            ->setAjaxUrl($this->controllerUrl() . 'tab/1');

        $tab1->setNoPadding()->setIcon('ti ti-timer');
        $tab2 = $tabList->addTab()->setLabel('Debug')
            ->setAjaxUrl($this->controllerUrl() . 'tab/2');
        $tab2->setIcon('ti ti-help-alt');

        $app->addH3()->add('Nested Tab');
        $tabList = $app->addTabList()->setTabPosition('top')->addClass('mb-3');
        $tab1 = $tabList->addTab()->setLabel('Parent Tab 1')
            ->setAjaxUrl($this->controllerUrl() . 'nested/1')->setNoPadding();
        $tab2 = $tabList->addTab()->setLabel('Parent Tab 2')
            ->setAjaxUrl($this->controllerUrl() . 'nested/2')->setNoPadding();

        return $app;
    }

    public function tab($no) {
        $app = c::app();
        $app->add('Tab Ajax No:' . $no);

        return $app;
    }

    public function nested($no) {
        $app = c::app();
        $tabList = $app->addTabList()->setTabPosition('left');
        $tab1 = $tabList->addTab()->setLabel('Activity ' . $no)
            ->setAjaxUrl($this->controllerUrl() . 'tab/' . $no . '-1');

        $tab1->setNoPadding()->setIcon('ti ti-timer');
        $tab2 = $tabList->addTab()->setLabel('Debug ' . $no)
            ->setAjaxUrl($this->controllerUrl() . 'tab/' . $no . '-2');
        $tab2->setIcon('ti ti-help-alt');

        return $app;
    }
}
