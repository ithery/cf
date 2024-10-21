<?php

use Rubix\ML\Estimator;

interface CML_Contract_ResultFillerInterface {
    public static function predict(string $modelPath, array $data, Estimator $estimator);
}
