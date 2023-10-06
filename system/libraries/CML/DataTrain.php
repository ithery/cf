<?php

class CML_DataTrain {
    const ESTIMATOR_TYPE_CLUSTERER = 'clusterer';

    protected $data;

    protected $ignoredAttributes;

    protected $dataIndexWithLabel;

    protected $estimator;

    /**
     * @var array
     */
    protected $transformers = [];

    protected $estimatorAlgorithm;

    public function __construct($data) {
        $this->data = $data;
    }

    public function setDataIndexWithLabel($dataIndexWithLabel) {
        $this->dataIndexWithLabel = $dataIndexWithLabel;

        return $this;
    }

    public function setIgnoredAttributes($attributes) {
        $this->ignoredAttributes = $attributes;

        return $this;
    }

    public function getData() {
        return $this->getDataArray();
    }

    public function getDataIndexWithLabel() {
        return $this->dataIndexWithLabel;
    }

    protected function getDataArray() {
        return $this->mixedToArray($this->data);
    }

    private function mixedToArray($data, array $ignore_attrs = null) {
        if (is_array($data)) {
            $rv = $data;
        } elseif ($data instanceof CCollection) {
            $rv = $data->toArray();
        } elseif ($data instanceof \iterable) {
            $rv = iterator_to_array($data);
        } else {
            throw new CML_Exception_GeneralException('Cannot convert this data type to array: ' . get_class($data));
        }
        $ignoredAttributes = $this->ignoredAttributes;
        if (!$ignoredAttributes) {
            foreach ($rv as &$v) {
                foreach ($ignoredAttributes as $i) {
                    if (isset($v[$i])) {
                        unset($v[$i]);
                    }
                }
            }
        }

        return $rv;
    }

    public function withClustererKMeans() {
        $this->estimator = CML::ESTIMATOR_CLUSTERER_KMEANS;
    }
}
