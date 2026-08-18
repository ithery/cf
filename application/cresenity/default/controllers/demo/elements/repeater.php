<?php

class Controller_Demo_Elements_Repeater extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Repeater');

        $this->basicRepeater($app);
        $this->selectSearchRepeater($app);
        $this->customizedRepeater($app);
        $this->maxItemRepeater($app);
        $this->readOnlyRepeater($app);

        return $app;
    }

    private function basicRepeater(CApp $app) {
        $widget = $app->addWidget()->setTitle('Basic Repeater');

        $repeater = $widget->addRepeater();
        $repeater->setMinItem(1);
        $repeater->setItemBuilder(function (CElement $item) {
            $row = $item->addDiv()->addClass('row');
            $row->addDiv()->addClass('col-md-6')
                ->addField()->setLabel('Name')->addTextControl('name[]');
            $row->addDiv()->addClass('col-md-6')
                ->addField()->setLabel('Email')->addEmailControl('email[]');
        });
    }

    private function selectSearchRepeater(CApp $app) {
        $widget = $app->addWidget()->setTitle('With SelectTwo (cres.js auto-init)');

        $form = $widget->addForm();
        $repeater = $form->addRepeater();
        $repeater->setMinItem(1);
        $repeater->setMaxItem(5);
        $repeater->setAddLabel('+ Add Item');
        $repeater->setItemBuilder(function (CElement $item) {
            $row = $item->addDiv()->addClass('row');

            $selectTwo = $row->addDiv()->addClass('col-md-5')
                ->addField()->setLabel('Country')
                ->addSelectTwoControl()
                ->setDataFromModel(\Cresenity\Demo\Model\Country::class);
            $selectTwo->setKeyField('code');
            $selectTwo->setSearchField('name');
            $selectTwo->setFormat('<div>{name} <span class="badge badge-info">{code}</span></div>');

            $row->addDiv()->addClass('col-md-4')
                ->addField()->setLabel('City')->addTextControl('city[]');

            $row->addDiv()->addClass('col-md-3')
                ->addField()->setLabel('Zip Code')->addTextControl('zip[]');
        });

        $form->addActionList()->addAction()->setLabel('Submit')->setSubmit();
    }

    private function customizedRepeater(CApp $app) {
        $widget = $app->addWidget()->setTitle('Customized Labels & Styles');

        $repeater = $widget->addRepeater();
        $repeater->setMinItem(1);
        $repeater->setAddLabel('+ Add Line Item');
        $repeater->setDeleteLabel('Remove');
        $repeater->setAddButtonClass('btn-outline-primary w-100');
        $repeater->setDeleteButtonClass('btn-outline-danger btn-sm');
        $repeater->setItemBuilder(function (CElement $item) {
            $row = $item->addDiv()->addClass('row');
            $row->addDiv()->addClass('col-md-4')
                ->addField()->setLabel('Product')->addTextControl('product[]');
            $row->addDiv()->addClass('col-md-3')
                ->addField()->setLabel('Quantity')->addTextControl('qty[]');
            $row->addDiv()->addClass('col-md-3')
                ->addField()->setLabel('Price')->addTextControl('price[]');
        });
    }

    private function maxItemRepeater(CApp $app) {
        $widget = $app->addWidget()->setTitle('Min 2 / Max 5 Items');

        $repeater = $widget->addRepeater();
        $repeater->setMinItem(2);
        $repeater->setMaxItem(5);
        $repeater->setAddLabel('Add Row (max 5)');
        $repeater->setItemBuilder(function (CElement $item) {
            $item->addField()->setLabel('Address')->addTextareaControl('address[]');
        });
    }

    private function readOnlyRepeater(CApp $app) {
        $widget = $app->addWidget()->setTitle('Add Only (No Delete)');

        $repeater = $widget->addRepeater();
        $repeater->setCanDelete(false);
        $repeater->setAddLabel('+ Add Phone Number');
        $repeater->setItemBuilder(function (CElement $item) {
            $row = $item->addDiv()->addClass('row');
            $row->addDiv()->addClass('col-md-4')
                ->addField()->setLabel('Type')->addSelectControl('phone_type[]')
                ->setList(['mobile' => 'Mobile', 'home' => 'Home', 'work' => 'Work']);
            $row->addDiv()->addClass('col-md-8')
                ->addField()->setLabel('Number')->addTextControl('phone[]');
        });
    }
}
