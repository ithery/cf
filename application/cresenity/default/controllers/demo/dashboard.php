<?php
use Cresenity\Demo\Model\Country as CountryModel;

class Controller_Demo_Dashboard extends \Cresenity\Demo\Controller {
    public function index() {
        $country = CountryModel::first();

        cdbg::dd($country);

        return c::app()->setTitle('Dashboard');
    }
}
