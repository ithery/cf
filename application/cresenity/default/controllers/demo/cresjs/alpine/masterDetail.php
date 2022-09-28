<?php

class Controller_Demo_Cresjs_Alpine_MasterDetail extends \Cresenity\Demo\Controller {
    /**
     * @return CApp
     */
    public function index() {
        c::manager()->registerModule('auto-numeric');
        $app = c::app();
        $items = [];
        $app->addView('demo.page.cresjs.alpine.master-detail', [
            'items' => $items
        ]);

        return $app;
    }

    public function json($countryId) {
        $country = Cresenity\Demo\Model\Country::find($countryId);
        $errCode = 0;
        $errMessage = '';
        $countryData = [
            'id' => $country->id,
            'name' => $country->name,
            'price' => 100,
            'qty' => 1,
            'subtotal' => 100,
        ];

        return CApp_Base::toJsonResponse($errCode, $errMessage, $countryData);
    }
}
