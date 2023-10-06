<?php

class CML {
    const ESTIMATOR_CLUSTERER_KMEANS = 'clusterer.kmeans';

    /**
     * @return CML_Trainer
     */
    public static function trainer() {
        return new CML_Trainer();
    }

    /**
     * @param array|CCollection|iterable $data
     *
     * @return CML_DataTrain
     */
    public static function createDataTrain($data) {
        return new CML_DataTrain($data);
    }
}
