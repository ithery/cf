<?php

use Rubix\ML\Estimator;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\AnomalyDetectors\Scoring;

class CML_Rubix_ResultFiller_AnomalyFiller implements CML_Contract_ResultFillerInterface {
    public static function predict($modelPath, array $data, Estimator $estimator): array {
        $anomalies = CML_Adapter_RubixAdapter::predict($modelPath, $data);

        if ($estimator instanceof Scoring) {
            $scores = $estimator->score(Unlabeled::build($data));
        }

        $can_score = $estimator instanceof Scoring;

        for ($i = 0, $iMax = count($data); $i < $iMax; $i++) {
            $data[$i]['anomaly'] = $anomalies[$i];

            if ($can_score && $scores ?? null) {
                $data[$i]['anomaly_score'] = $scores[$i];
            }
        }

        if ($can_score) {
            usort(
                $data,
                function ($a, $b) {
                    return $b['anomaly_score'] <=> $a['anomaly_score'];
                }
            );
        } else {
            usort(
                $data,
                function ($a, $b) {
                    return $a['anomaly'] <=> $b['anomaly'];
                }
            );
        }

        return $data;
    }
}
