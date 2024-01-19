<?php

class Controller_Demo_Listener_Dependson extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Depends On');
        $app->addH5()->add('Div dependsOn from select');
        $countrySelect = $app->addField()->setLabel('Select Country')->addSelectControl()
            ->setList(\Cresenity\Demo\Model\Country::get()->pluck('name', 'id')->all());
        $div = $app->addDiv();
        $div->setDependsOn($countrySelect, function ($value) {
            $div = c::div();
            $countryModel = \Cresenity\Demo\Model\Country::find($value);
            if ($countryModel) {
                $div->addP()->add('ID : ' . $countryModel->getKey());
                $div->addP()->add('Code : ' . $countryModel->code);
                $div->addP()->add('Name : ' . $countryModel->name);
            } else {
                $div->addP()->add('<span style="color:#f00">Country Not Found</span>');
            }

            return $div;
        });
        $app->addHr();
        $app->addH5()->addClass('mt-4')->add('Select dependsOn from select');
        $continentArray = \Cresenity\Demo\Model\Country::get()->pluck('continent')->unique()->all();
        $continentList = c::collect($continentArray)->combine($continentArray);
        $continentSelect = $app->addField()->setLabel('Select Continent')->addSelectControl()
            ->setList($continentList);
        $countrySelect = $app->addField()->setLabel('Select Country')->addSelectControl()
            ->setDependsOn($continentSelect, function ($value) {
                return \Cresenity\Demo\Model\Country::where('continent', '=', $value)->get()->map(function ($country) {
                    return [
                        'key' => carr::get($country, 'id'),
                        'value' => carr::get($country, 'name'),
                    ];
                })->all();
            });

        return $app;
    }
}
