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
     * @param mixed $topK
     * @param mixed $maxItemPerTransaction
     *
     * @return float[] similarity scores, same order/keys as $candidates
     */
    /**
     * Item-to-item collaborative filtering: kemiripan antar item dihitung
     * dari transaksi yang memuatnya bersama-sama.
     *
     * Memakai cosine (Rubix\ML\Kernels\Distance\Cosine) atas vektor
     * kemunculan tiap item pada transaksi, bukan sekadar menghitung berapa
     * kali dua item muncul bersama. Bedanya menentukan: hitungan mentah
     * membuat item terlaris tampak berkaitan dengan hampir segalanya, karena
     * ia memang ikut hampir di setiap transaksi. Cosine menormalkan terhadap
     * populernya masing-masing, sehingga yang tersisa keterkaitan yang
     * sesungguhnya.
     *
     * Transaksi kecil diberi bobot lebih besar (1/(n-1)): dua barang yang
     * dibeli berdua jauh lebih menunjukkan keterkaitan daripada dua barang
     * yang kebetulan sama-sama ada di keranjang berisi tiga puluh barang.
     *
     * Hanya pasangan yang pernah muncul bersama yang dihitung — pasangan
     * lain cosine-nya nol, jadi menghitungnya hanya membuang waktu O(n^2).
     *
     * @param array $transactions          daftar transaksi, tiap transaksi daftar id item
     * @param int   $topK                  jumlah item terkait yang dikembalikan per item
     * @param int   $maxItemPerTransaction transaksi dengan item lebih banyak
     *                                     dari ini dilewati - pasangannya
     *                                     meledak kuadratik dan keranjang
     *                                     sebesar itu jarang belanja biasa
     *
     * @return array id item => [id item terkait => skor 0..1], terurut menurun
     */
    public static function itemCollaborativeSimilarity(array $transactions, $topK = 20, $maxItemPerTransaction = 40) {
        $vectors = [];
        $pairs = [];

        foreach ($transactions as $index => $itemIds) {
            $itemIds = array_values(array_unique(array_filter($itemIds)));
            $size = count($itemIds);
            if ($size < 2 || $size > $maxItemPerTransaction) {
                continue;
            }

            $weight = 1 / ($size - 1);
            foreach ($itemIds as $itemId) {
                $vectors[$itemId][$index] = $weight;
            }
            for ($i = 0; $i < $size; $i++) {
                for ($j = $i + 1; $j < $size; $j++) {
                    $pairs[$itemIds[$i]][$itemIds[$j]] = true;
                    $pairs[$itemIds[$j]][$itemIds[$i]] = true;
                }
            }
        }

        if (count($vectors) == 0) {
            return [];
        }

        $cosine = new Rubix\ML\Kernels\Distance\Cosine();
        $result = [];

        foreach ($pairs as $itemId => $candidates) {
            $scores = [];
            foreach (array_keys($candidates) as $otherId) {
                //Rubix menuntut kedua vektor sepanjang dan seurut sama, jadi
                //vektor jarang di atas dipadatkan hanya pada transaksi yang
                //disentuh salah satu dari keduanya - hasilnya sama dengan
                //memadatkan pada seluruh transaksi, tanpa biayanya.
                $keys = $vectors[$itemId] + $vectors[$otherId];
                $a = [];
                $b = [];
                foreach (array_keys($keys) as $key) {
                    $a[] = carr::get($vectors[$itemId], $key, 0);
                    $b[] = carr::get($vectors[$otherId], $key, 0);
                }

                $similarity = 1 - $cosine->compute($a, $b);
                if ($similarity > 0) {
                    $scores[$otherId] = $similarity;
                }
            }

            if (count($scores) == 0) {
                continue;
            }
            arsort($scores);
            $result[$itemId] = array_slice($scores, 0, $topK, true);
        }

        return $result;
    }

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
