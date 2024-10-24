<?php

class Controller_Demo_Controls_Select_Search extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $form = $app->addForm();
        $post = CApp_Base::getRequestPost();
        if ($post) {
            $app->addAlert()->setTypeSuccess()->addAlert()->add(json_encode($post, JSON_PRETTY_PRINT));
        }
        $app->setTitle('Select Search');
        $div = $form->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Simple Select Search');
        $selectSearch = $div->addSelectSearchControl('select_simple')->setDataFromModel(\Cresenity\Demo\Model\Country::class);
        $selectSearch->setKeyField('country_id');
        $selectSearch->setSearchField('name');

        $div = $form->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Select Search With Format');
        $selectSearch = $div->addSelectSearchControl('select_with_format')->setDataFromModel(\Cresenity\Demo\Model\Country::class);
        $selectSearch->setKeyField('id');
        $selectSearch->setSearchField('name');
        $selectSearch->setFormat('<div>{name}</div><div><span class="badge badge-success">{code}</span></div>');
        $selectSearch->setValue(c::optional(\Cresenity\Demo\Model\Country::where('code', '=', 'ID')->first())->id);
        $div = $form->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Select Search Multiple with key name');
        $selectSearch = $div->addSelectSearchControl('select_multiple_with_name')
            ->setName('select_multiple_with_name[]')->setDataFromModel(\Cresenity\Demo\Model\Country::class);
        $selectSearch->setKeyField('name');
        $selectSearch->setSearchField('name');
        $selectSearch->setMultiple();
        $selectSearch->setFormat(function (Cresenity\Demo\Model\Country $country) {
            $div = c::div();
            $div->addDiv()->add($country->name);
            $divDesc = $div->addDiv();
            $divDesc->addSpan()->addClass('badge badge-success mr-1')->add($country->code);
            $divDesc->addSpan()->addClass('badge badge-info mr-1')->add($country->continent);

            return $div;
        });

        $form->addActionList()->addAction()->setLabel('Submit')->setSubmit();

        return $app;
    }
}
