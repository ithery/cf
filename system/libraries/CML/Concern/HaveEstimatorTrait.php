<?php

trait CML_Concern_HaveEstimatorTrait {
    protected $estimator;

    protected $estimatorParameters;

    /**
     * @param mixed $k
     *
     * @return CML_DataTrain
     */
    public function withClustererKMeansEstimator($k = 5) {
        $this->estimator = CML::ESTIMATOR_CLUSTERER_KMEANS;
        $this->estimatorParameters = [
            'k' => $k
        ];

        return $this;
    }

    /**
     * @param string $estimator
     * @param array  $estimatorParameters
     *
     * @return static
     */
    public function setEstimator($estimator, $estimatorParameters = []) {
        $this->estimator = $estimator;
        $this->estimatorParameters = $estimatorParameters;

        return $this;
    }

    /**
     * @return string
     */
    public function getEstimator() {
        return $this->estimator;
    }

    /**
     * @return array
     */
    public function getEstimatorParameters() {
        return $this->estimatorParameters;
    }
}
