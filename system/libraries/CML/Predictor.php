<?php

class CML_Predictor {
    public function predict(CML_DataPredict $dataPredict) {
        $data = $dataPredict->getData();

        $adapter = CML_Manager::instance()->createRubixAdapter();
        $modelRepository = CML_Manager::instance()->getModelRepository($dataPredict->getModelPath());
        $path = $modelRepository->file($dataPredict->getModelFile());

        return $adapter->predict(
            $path,
            $data,
        );
    }
}
