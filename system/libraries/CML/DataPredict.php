<?php

class CML_DataPredict {
    use CML_Concern_HaveModelTrait;
    use CML_Concern_HaveDataTrait;
    use CML_Concern_HaveEstimatorTrait;

    public function __construct($data) {
        $this->data = $data;
        $this->modelPath = CF::config('ml.ai_model_path_output');
    }
}
