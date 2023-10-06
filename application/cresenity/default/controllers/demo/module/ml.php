<?php

class Controller_Demo_Module_Ml extends \Cresenity\Demo\Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data = CML::createDataTrain(Cresenity\Demo\Model\Country::all());
        $result = CML::trainer()->train($data);

        cdbg::dd($result);
    }
}
