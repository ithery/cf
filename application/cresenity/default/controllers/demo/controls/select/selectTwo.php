<?php

class Controller_Demo_Controls_Select_SelectTwo extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Select Two');

        $form = $app->addForm();
        $post = CApp_Base::getRequestPost();
        if ($post) {
            $app->addAlert()->setTypeSuccess()->add(json_encode($post, JSON_PRETTY_PRINT));
        }

        $div = $form->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Simple Select Two');
        $selectTwo = $div->addSelectTwoControl('select_simple')
            ->setDataFromModel(\Cresenity\Demo\Model\Country::class);
        $selectTwo->setKeyField('country_id');
        $selectTwo->setSearchField('name');

        $div = $form->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Select Two With Format');
        $selectTwo = $div->addSelectTwoControl('select_with_format')
            ->setDataFromModel(\Cresenity\Demo\Model\Country::class);
        $selectTwo->setKeyField('id');
        $selectTwo->setSearchField('name');
        $selectTwo->setFormat('<div>{name}</div><div><span class="badge badge-success">{code}</span></div>');
        $selectTwo->setValue(c::optional(\Cresenity\Demo\Model\Country::where('code', '=', 'ID')->first())->id);

        $div = $form->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Select Two Multiple');
        $selectTwo = $div->addSelectTwoControl('select_multiple')
            ->setName('select_multiple[]')
            ->setDataFromModel(\Cresenity\Demo\Model\Country::class);
        $selectTwo->setKeyField('name');
        $selectTwo->setSearchField('name');
        $selectTwo->setMultiple();
        $selectTwo->setFormat('<div>{name} <span class="badge badge-success">{code}</span> <span class="badge badge-info">{continent}</span></div>');

        $div = $form->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Select Two With Allow Clear');
        $selectTwo = $div->addSelectTwoControl('select_allow_clear')
            ->setDataFromModel(\Cresenity\Demo\Model\Country::class);
        $selectTwo->setKeyField('code');
        $selectTwo->setSearchField('name');
        $selectTwo->setAllowClear(true);
        $selectTwo->setPlaceholder('Choose a country...');

        $div = $form->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Select Two With Min Input Length');
        $selectTwo = $div->addSelectTwoControl('select_min_input')
            ->setDataFromModel(\Cresenity\Demo\Model\Country::class);
        $selectTwo->setKeyField('code');
        $selectTwo->setSearchField('name');
        $selectTwo->setMinInputLength(2);
        $selectTwo->setPlaceholder('Type at least 2 characters...');

        $div = $form->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Select Two With Prepend Data');
        $selectTwo = $div->addSelectTwoControl('select_prepend')
            ->setDataFromModel(\Cresenity\Demo\Model\Country::class);
        $selectTwo->setKeyField('code');
        $selectTwo->setSearchField('name');
        $selectTwo->setPrependData([
            ['code' => 'ALL', 'name' => '-- All Countries --'],
            ['code' => 'CUSTOM', 'name' => '-- Custom Entry --'],
        ]);

        $div = $form->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Select Two With Depends On');
        $selectContinent = $div->addSelectControl('select_continent');
        $selectContinent->setList([
            '' => '-- Select Continent --',
            'Asia' => 'Asia',
            'Europe' => 'Europe',
            'Africa' => 'Africa',
            'North America' => 'North America',
            'South America' => 'South America',
            'Oceania' => 'Oceania',
        ]);
        $selectCountryDepends = $div->addSelectTwoControl('select_depends')
            ->setDataFromModel(\Cresenity\Demo\Model\Country::class);
        $selectCountryDepends->setKeyField('code');
        $selectCountryDepends->setSearchField('name');
        $selectCountryDepends->setPlaceholder('Select country...');
        $selectCountryDepends->setDependsOn($selectContinent, function ($q, $value) {
            if (strlen($value) > 0) {
                $q->where('continent', '=', $value);
            }
        });

        $form->addActionList()->addAction()->setLabel('Submit')->setSubmit();

        return $app;
    }
}
