<?php

use Rubix\ML\Estimator;

interface CML_Contract_ResultFillerInterface {
    public static function predict(array $data, Estimator $estimator);
}
