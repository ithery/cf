<?php

class Controller_Demo_Model_Lazy extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Lazy Model');
        $countryModel = new Cresenity\Demo\Model\Country();
        $countryModel->getConnection()->disableBenchmark();
        $query = $countryModel->query();
        $data = $query->lazy(5);
        $countryModel->getConnection()->enableBenchmark();
        foreach ($data as $d) {
        }

        $app->add($countryModel->getConnection()->getBenchmarks());

        return $app;
    }
}
