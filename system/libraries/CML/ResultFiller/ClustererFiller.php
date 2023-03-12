<?php

use Rubix\ML\Estimator;

class CML_ResultFiller_ClustererFiller implements CML_Contract_ResultFillerInterface {
    public static function predict(array $data, Estimator $estimator) {
        $clusters = CML_Rubix::predict($data);

        for ($i = 0, $iMax = count($data); $i < $iMax; $i++) {
            $data[$i]['cluster_nr'] = $clusters[$i];
        }

        usort(
            $data,
            function ($a, $b) {
                return $a['cluster_nr'] <=> $b['cluster_nr'];
            }
        );

        return $data;
    }
}
