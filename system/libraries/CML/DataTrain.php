<?php

class CML_DataTrain {
    use CML_Concern_HaveModelTrait;
    use CML_Concern_HaveDataTrait;
    use CML_Concern_HaveEstimatorTrait;

    const ESTIMATOR_TYPE_CLUSTERER = 'clusterer';

    protected $dataIndexWithLabel;

    /**
     * @var array
     */
    protected $transformers = null;

    public function __construct($data) {
        $this->data = $data;
        $this->modelPath = CF::config('ml.ai_model_path_output');
    }

    public function setDataIndexWithLabel($dataIndexWithLabel) {
        $this->dataIndexWithLabel = $dataIndexWithLabel;

        return $this;
    }

    public function setIgnoredAttributes($attributes) {
        $this->ignoredAttributes = $attributes;

        return $this;
    }

    public function getDataIndexWithLabel() {
        return $this->dataIndexWithLabel;
    }

    public function getTransformers() {
        return $this->transformers;
    }

    public function getDataPredict($data = null) {
        return (new CML_DataPredict($data ?: $this->getData()))
            ->setModelFile($this->modelFile, $this->modelPath)
            ->setEstimator($this->estimator, $this->estimatorParameters);
    }
}
