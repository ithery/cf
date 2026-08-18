<?php

use PHPUnit\Framework\TestCase;

/**
 * CML::associationRules() — Apriori beserta support, confidence, dan lift.
 *
 * Ketiga ukuran itu diuji bersama karena masing-masing sendirian menyesatkan,
 * dan yang paling mudah keliru justru **lift**: tanpa membaginya dengan
 * support consequent, barang terlaris akan muncul sebagai "aturan kuat" di
 * mana-mana semata karena hampir semua orang membelinya.
 */
class AssociationRulesTest extends TestCase {
    /**
     * @param int $consequent
     *
     * @return null|array
     */
    protected function findRule(array $rules, array $antecedent, $consequent) {
        foreach ($rules as $rule) {
            if ($rule['consequent'] == $consequent && $rule['antecedent'] == $antecedent) {
                return $rule;
            }
        }

        return null;
    }

    /**
     * Pasangan yang selalu muncul bersama: confidence penuh, dan lift jauh di
     * atas 1 karena kehadiran satu benar-benar menentukan yang lain.
     */
    public function testAlwaysCoOccurringPairHasFullConfidence() {
        $rules = CML::associationRules([
            [1, 2],
            [1, 2],
            [1, 2],
            [3, 4],
        ], 0.1, 0.1, 2);

        $rule = $this->findRule($rules, [1], 2);

        $this->assertNotNull($rule, 'aturan 1 -> 2 tidak ditemukan');
        $this->assertEqualsWithDelta(1.0, $rule['confidence'], 0.0001, 'confidence bukan penuh padahal selalu bersama');
        $this->assertEqualsWithDelta(0.75, $rule['support'], 0.0001, 'support salah');
        $this->assertGreaterThan(1.0, $rule['lift'], 'lift tidak menunjukkan keterkaitan positif');
    }

    /**
     * Inilah gunanya lift. Item 99 ada di setiap transaksi, jadi aturan
     * "beli apa pun -> beli 99" punya confidence sempurna dan terlihat kuat —
     * padahal tidak mengandung keterkaitan apa pun. Lift-nya harus 1.
     */
    public function testUbiquitousItemGetsLiftOfOneDespitePerfectConfidence() {
        $rules = CML::associationRules([
            [1, 99],
            [2, 99],
            [3, 99],
            [4, 99],
        ], 0.2, 0.5, 2);

        $rule = $this->findRule($rules, [1], 99);

        $this->assertNotNull($rule, 'aturan 1 -> 99 tidak ditemukan');
        $this->assertEqualsWithDelta(1.0, $rule['confidence'], 0.0001, 'prasyarat: confidence-nya memang sempurna');
        $this->assertEqualsWithDelta(
            1.0,
            $rule['lift'],
            0.0001,
            'lift tidak 1 untuk item yang ada di semua transaksi — barang terlaris akan tampak berkaitan dengan segalanya'
        );
    }

    /**
     * Kombinasi yang jarang muncul dipangkas sebelum menjadi aturan.
     */
    public function testRareCombinationsArePrunedByMinSupport() {
        $transactions = [];
        for ($i = 0; $i < 20; $i++) {
            $transactions[] = [1, 2];
        }
        $transactions[] = [7, 8];

        $rules = CML::associationRules($transactions, 0.2, 0.1, 2);

        $this->assertNull($this->findRule($rules, [7], 8), 'kombinasi langka lolos ambang support');
        $this->assertNotNull($this->findRule($rules, [1], 2), 'kombinasi yang sering justru ikut terbuang');
    }

    /**
     * Aturan dengan confidence di bawah ambang tidak dikembalikan.
     */
    public function testLowConfidenceRulesAreDropped() {
        //1 muncul 4x, hanya sekali bersama 2 -> confidence 0.25
        $rules = CML::associationRules([
            [1, 2],
            [1, 3],
            [1, 4],
            [1, 5],
        ], 0.2, 0.5, 2);

        $this->assertNull($this->findRule($rules, [1], 2), 'aturan berconfidence rendah ikut dikembalikan');
    }

    /**
     * Antecedent boleh lebih dari satu item — itu justru bentuk yang berguna
     * untuk bundling ("beli A dan B, biasanya ikut C").
     */
    public function testMultiItemAntecedentIsProduced() {
        $rules = CML::associationRules([
            [1, 2, 3],
            [1, 2, 3],
            [1, 2, 3],
            [1, 2],
        ], 0.2, 0.5, 3);

        $rule = $this->findRule($rules, [1, 2], 3);

        $this->assertNotNull($rule, 'aturan berantecedent dua item tidak dihasilkan');
        $this->assertEqualsWithDelta(0.75, $rule['confidence'], 0.0001, 'confidence antecedent ganda salah');
    }

    /**
     * maxItemsetSize benar-benar membatasi kedalaman.
     */
    public function testMaxItemsetSizeIsRespected() {
        $rules = CML::associationRules([
            [1, 2, 3],
            [1, 2, 3],
            [1, 2, 3],
        ], 0.2, 0.1, 2);

        foreach ($rules as $rule) {
            $this->assertLessThanOrEqual(1, count($rule['antecedent']), 'antecedent melebihi batas ukuran kombinasi');
        }
    }

    /**
     * Terurut menurun menurut lift, karena itulah yang dibaca lebih dulu.
     */
    public function testRulesAreSortedByLiftDescending() {
        $rules = CML::associationRules([
            [1, 2],
            [1, 2],
            [3, 4],
            [3, 5],
            [3, 6],
            [3, 4],
        ], 0.1, 0.1, 2);

        $lifts = array_column($rules, 'lift');
        $sorted = $lifts;
        rsort($sorted);

        $this->assertSame($sorted, $lifts, 'aturan tidak terurut menurun menurut lift');
    }

    /**
     * Masukan kosong tidak boleh melempar.
     */
    public function testEmptyInputReturnsEmpty() {
        $this->assertSame([], CML::associationRules([]));
    }
}
