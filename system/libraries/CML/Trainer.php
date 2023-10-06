<?php

class CML_Trainer {
    /**
     * @var CML_Trainer
     */
    private static $instance;

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function train(CML_DataTrain $dataTrain) {
        $data = $dataTrain->getData();
    }
}
