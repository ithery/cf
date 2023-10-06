<?php

use Rubix\ML\DataType;
use Rubix\ML\Pipeline;
use Rubix\ML\Estimator;
use Rubix\ML\Extractors\CSV;

use Rubix\ML\Loggers\Screen;
use Rubix\ML\PersistentModel;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Classifiers\KDNeighbors;
use Rubix\ML\Extractors\ColumnPicker;
use Rubix\ML\Transformers\Transformer;
use Rubix\ML\Transformers\OneHotEncoder;
use Rubix\ML\CrossValidation\Metrics\MCC;
use Rubix\ML\CrossValidation\Metrics\FBeta;
use Rubix\ML\Transformers\MinMaxNormalizer;
use Rubix\ML\Regressors\KDNeighborsRegressor;
use Rubix\ML\Transformers\MissingDataImputer;
use Rubix\ML\Transformers\NumericStringConverter;
use Rubix\ML\CrossValidation\Metrics\Informedness;
use Rubix\ML\CrossValidation\Reports\ErrorAnalysis;

class CML_Rubix {
    public static function getEstimatorClassMaps() {
        return [
            'clusterer' => [
                'kmeans' => \Rubix\ML\Clusterers\KMeans::class
            ]
        ];
    }

    /**
     * @param string $estimator
     * @param array  $parameters
     *
     * @return \Rubix\ML\Estimator;
     */
    public static function createEstimator($estimator, $parameters = []) {
        return c::container()->make($estimator, $parameters);
    }
}
