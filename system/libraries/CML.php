<?php

class CML {
    const ESTIMATOR_CLUSTERER_KMEANS = 'clusterer.kmeans';

    /**
     * @return CML_Trainer
     */
    public static function trainer() {
        return new CML_Trainer();
    }

    /**
     * @return CML_Predictor
     */
    public static function predictor() {
        return new CML_Predictor();
    }

    /**
     * @param array|CCollection|iterable $data
     *
     * @return CML_DataTrain
     */
    public static function createDataTrain($data) {
        return new CML_DataTrain($data);
    }

    /**
     * @param array|CCollection|iterable $data
     *
     * @return CML_DataPredict
     */
    public static function createDataPredict($data) {
        return new CML_DataPredict($data);
    }

    /**
     * @return CML_Manager
     */
    public static function manager() {
        return CML_Manager::instance();
    }

    public static function modelRepository($path = null) {
        return static::manager()->getModelRepository($path);
    }

    /**
     * Cosine similarity (0-1, higher = more similar) between $text and each
     * of $candidates, via TF-IDF vectors (Rubix\ML\Transformers\
     * WordCountVectorizer + TfIdfTransformer + Kernels\Distance\Cosine) -
     * fits the vocabulary once across $text + $candidates together so
     * scores are comparable. Word-overlap based, not semantic: catches
     * reworded/reordered near-duplicates, misses true paraphrases that
     * share few words.
     *
     * @param string   $text
     * @param string[] $candidates
     *
     * @return float[] similarity scores, same order/keys as $candidates
     */
    public static function textSimilarity($text, array $candidates) {
        if (empty($candidates)) {
            return [];
        }

        $keys = array_keys($candidates);
        $samples = array_map(function ($value) {
            return [(string) $value];
        }, array_merge([$text], array_values($candidates)));

        $dataset = Rubix\ML\Datasets\Unlabeled::build($samples);
        $vectorizer = new Rubix\ML\Transformers\WordCountVectorizer();
        $vectorizer->fit($dataset);
        $dataset->apply($vectorizer);
        $tfidf = new Rubix\ML\Transformers\TfIdfTransformer();
        $tfidf->fit($dataset);
        $dataset->apply($tfidf);

        $vectors = $dataset->samples();
        $needle = $vectors[0];
        $cosine = new Rubix\ML\Kernels\Distance\Cosine();

        $scores = [];
        foreach ($keys as $i => $key) {
            $scores[$key] = 1 - $cosine->compute($needle, $vectors[$i + 1]);
        }

        return $scores;
    }
}
