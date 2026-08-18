<?php

use PHPUnit\Framework\TestCase;

/**
 * Menjaga dua keputusan pada RelationExistenceRule yang mudah "diperbaiki"
 * keliru oleh orang berikutnya.
 *
 * Pertama, daftar methodnya harus benar-benar ada di CF. Menyalin daftar
 * Larastan apa adanya akan memuat nama yang tidak dikenal CF, dan rule yang
 * memeriksa method tak bernama apa pun hanya membuang waktu analisis.
 *
 * Kedua - dan ini yang penting - rule TIDAK boleh menuntut tipe kembalian
 * sebuah method relasi berupa CModel_Relation. Larastan menuntutnya karena di
 * Laravel relasi hampir selalu beranotasi; di CF ia lazim dirantai
 * (`belongsTo(...)->withTrashed()`) dan `@return`-nya jarang ditulis, sehingga
 * tuntutan itu melaporkan relasi yang benar-benar ada sebagai hilang - 19 di
 * antaranya terukur pada ohayomart, 2026-08-18. Menghidupkannya kembali akan
 * menghidupkan kembali salah lapor itu.
 *
 * Berkasnya dibaca sebagai teks, sama seperti PhpstanExtensionRegistrationTest:
 * kelas CQC_Phpstan memakai sintaks PHP 8 sedangkan test berjalan di 7.4.
 */
class RelationExistenceRuleTest extends TestCase {
    /**
     * @return string
     */
    protected function ruleSource() {
        return file_get_contents(
            dirname(__DIR__, 2) . '/system/libraries/CQC/Phpstan/Service/Rule/RelationExistenceRule.php'
        );
    }

    /**
     * @return string[]
     */
    protected function methodList() {
        $matched = preg_match('/const METHOD = \[(.*?)\];/s', $this->ruleSource(), $block);
        $this->assertSame(1, $matched, 'Blok const METHOD tidak ditemukan');

        preg_match_all("/'([a-zA-Z]+)'/", $block[1], $names);

        return $names[1];
    }

    public function testEveryCheckedMethodExistsInTheFramework() {
        $source = file_get_contents(dirname(__DIR__, 2) . '/system/libraries/CModel/Trait/QueriesRelationships.php')
            . file_get_contents(dirname(__DIR__, 2) . '/system/libraries/CModel/Query.php');

        $missing = [];
        foreach ($this->methodList() as $method) {
            if (strpos($source, 'function ' . $method . '(') === false) {
                $missing[] = $method;
            }
        }

        $this->assertSame([], $missing, 'Method yang diperiksa tetapi tidak ada di CF: ' . implode(', ', $missing));
    }

    public function testTheListCoversTheMethodsThatMatter() {
        $list = $this->methodList();

        foreach (['whereHas', 'with', 'withCount', 'has'] as $method) {
            $this->assertContains($method, $list, $method . ' harus ikut diperiksa');
        }
    }

    public function testRuleDoesNotDemandARelationReturnType() {
        $source = $this->ruleSource();

        //penjaga yang sebenarnya: pemeriksaan tipe kembalian tidak boleh
        //dihidupkan lagi di dalam relationMethod()
        $matched = preg_match('/private function relationMethod\(.*?\n    \}/s', $source, $body);
        $this->assertSame(1, $matched, 'Method relationMethod() tidak ditemukan');

        $this->assertStringNotContainsString(
            'CModel_Relation::class',
            $body[0],
            'relationMethod() kembali menuntut tipe kembalian CModel_Relation - itu melaporkan relasi yang ada sebagai hilang'
        );
    }

    public function testBaseModelIsSkipped() {
        $source = $this->ruleSource();

        //`TBModel::make('Member')` menghasilkan tipe kelas induk, dan relasi
        //model sebenarnya tidak mungkin diketahui dari situ. Tanpa penjaga ini
        //19 relasi yang ada dilaporkan hilang di tribelio.
        $this->assertStringContainsString(
            "strpos(\$modelReflection->getName(), '_') === false",
            $source,
            'Penjaga kelas induk hilang - idiom {Prefix}Model::make() akan melahirkan salah lapor lagi'
        );
    }

    public function testColumnSelectionAndAliasAreStrippedFromTheName() {
        $source = $this->ruleSource();

        //`with('user:id,name')` dan `withCount('post as total')` harus dipangkas
        //sebelum dicocokkan, kalau tidak keduanya dilaporkan hilang
        $this->assertStringContainsString("explode(':', \$name)", $source);
        $this->assertStringContainsString('as\\s+', $source);
    }
}
