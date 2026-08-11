<?php

use PHPUnit\Framework\TestCase;

/**
 * CML::itemCollaborativeSimilarity() — kemiripan antar item dari transaksi
 * yang memuatnya bersama.
 *
 * Yang dijaga bukan angka pastinya, melainkan sifat-sifat yang membuat
 * rekomendasinya masuk akal. Yang paling menentukan: **normalisasi terhadap
 * populernya sebuah item**. Tanpa itu barang terlaris tampak berkaitan dengan
 * hampir segalanya, semata karena ia ikut di hampir setiap transaksi — gejala
 * yang tidak terlihat sebagai galat, hanya sebagai rekomendasi yang mendatar
 * dan tidak berguna.
 */
class CollaborativeSimilarityTest extends TestCase {
    /**
     * Dua item yang selalu muncul bersama, dan tidak pernah tanpa satu sama
     * lain, semirip-miripnya.
     */
    public function testItemsAlwaysBoughtTogetherScoreHighest() {
        $result = CML::itemCollaborativeSimilarity([
            [1, 2],
            [1, 2],
            [1, 2],
        ]);

        $this->assertEqualsWithDelta(1.0, $result[1][2], 0.0001, 'pasangan yang selalu bersama tidak mendapat skor tertinggi');
    }

    /**
     * Keterkaitan tidak berarah — kalau hanya satu arah tercatat, separuh
     * produk kehilangan rekomendasinya tergantung urutan id.
     */
    public function testSimilarityIsSymmetric() {
        $result = CML::itemCollaborativeSimilarity([[1, 2], [2, 3]]);

        $this->assertEqualsWithDelta($result[1][2], $result[2][1], 0.0001, 'skor kedua arah berbeda');
    }

    /**
     * Inilah alasan memakai cosine, bukan hitungan mentah.
     *
     * Item 99 laris: ikut di semua transaksi. Item 1 dan 2 hanya muncul
     * berdua. Hitungan mentah akan menempatkan 99 setara atau lebih tinggi
     * karena frekuensinya besar; cosine menempatkan pasangan sejati di atas.
     */
    public function testPopularItemDoesNotOutrankAGenuinePair() {
        $result = CML::itemCollaborativeSimilarity([
            [1, 2, 99],
            [3, 99],
            [4, 99],
            [5, 99],
            [6, 99],
        ]);

        $this->assertGreaterThan(
            $result[1][99],
            $result[1][2],
            'item terlaris mengalahkan pasangan sejati — rekomendasi akan didominasi barang populer'
        );
    }

    /**
     * Item tidak pernah merekomendasikan dirinya sendiri.
     */
    public function testItemNeverRecommendsItself() {
        $result = CML::itemCollaborativeSimilarity([[1, 2, 1, 3]]);

        foreach ($result as $itemId => $related) {
            $this->assertArrayNotHasKey($itemId, $related, 'item ' . $itemId . ' merekomendasikan dirinya sendiri');
        }
    }

    /**
     * Transaksi berisi satu barang tidak memuat keterkaitan apa pun, dan
     * transaksi raksasa dilewati karena pasangannya meledak kuadratik.
     */
    public function testSingleItemAndOversizedTransactionsAreIgnored() {
        $this->assertSame([], CML::itemCollaborativeSimilarity([[1]]), 'transaksi satu barang ikut dihitung');

        $besar = range(1, 12);
        $this->assertSame([], CML::itemCollaborativeSimilarity([$besar], 20, 10), 'transaksi melebihi batas ikut dihitung');
    }

    /**
     * topK membatasi banyaknya hasil per item, mengambil yang teratas.
     */
    public function testTopKLimitsResultsPerItem() {
        $result = CML::itemCollaborativeSimilarity([[1, 2, 3, 4, 5]], 2);

        $this->assertCount(2, $result[1], 'topK tidak membatasi jumlah hasil');
    }

    /**
     * Hasil terurut menurun, karena pembacanya menampilkan apa adanya.
     */
    public function testResultsAreSortedByScoreDescending() {
        $result = CML::itemCollaborativeSimilarity([
            [1, 2],
            [1, 2],
            [1, 3],
        ]);

        $scores = array_values($result[1]);
        $sorted = $scores;
        rsort($sorted);

        $this->assertSame($sorted, $scores, 'hasil tidak terurut menurun');
    }

    /**
     * Masukan kosong tidak boleh melempar.
     */
    public function testEmptyInputReturnsEmpty() {
        $this->assertSame([], CML::itemCollaborativeSimilarity([]));
    }
}
