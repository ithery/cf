<?php

class Controller_Demo_Module_Ml extends \Cresenity\Demo\Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data = CML::createDataTrain(Cresenity\Demo\Model\Country::all())
            ->withClustererKMeansEstimator(5)
            ->setModelFile('country.rbx');
        $resultTrain = CML::trainer()->train($data);
        $firstData = Cresenity\Demo\Model\Country::first();
        $firstDataPredict = $data->getDataPredict($firstData);
        $resultPredict = CML::predictor()->predict($firstDataPredict);
        cdbg::dd($resultPredict, $firstData, c::collect($resultTrain)->where('name', '=', $firstData->name));
    }
}
