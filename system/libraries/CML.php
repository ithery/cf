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
     * @return CML_Predictor
     */
    public static function predictor() {
        return new CML_Predictor();
    }

    /**
     * @param array|CCollection|iterable $data
     *
     * @return CML_DataTrain
     */
    public static function createDataTrain($data) {
        return new CML_DataTrain($data);
    }

    /**
     * @param array|CCollection|iterable $data
     *
     * @return CML_DataPredict
     */
    public static function createDataPredict($data) {
        return new CML_DataPredict($data);
    }

    /**
     * @return CML_Manager
     */
    public static function manager() {
        return CML_Manager::instance();
    }

    public static function modelRepository($path = null) {
        return static::manager()->getModelRepository($path);
    }
}
