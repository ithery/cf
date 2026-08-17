<?php

use PHPUnit\Framework\TestCase;

/**
 * Menjaga daftar MANY_RELATION di ModelPropertyExtension tetap cocok dengan
 * kelas relasinya sendiri.
 *
 * Ekstensi itu memutuskan `$model->sesuatu` menghasilkan koleksi atau satu
 * model dari daftar tertutup, sebab relasi CF tidak punya template `TResult`
 * seperti Laravel. Daftar tertutup punya satu kelemahan: ia diam saja ketika
 * kelas relasi baru ditambahkan. Yang salah klasifikasi tidak menimbulkan galat
 * apa pun - PHPStan hanya menyimpulkan tipe yang keliru, dan itu justru lebih
 * berbahaya daripada tidak menyimpulkan sama sekali.
 *
 * Klasifikasinya diperiksa dari sumber yang sama dengan saat daftar itu
 * disusun: isi `getResults()` masing-masing relasi. Yang memanggil `->get()`
 * atau `newCollection()` menghasilkan koleksi; yang memanggil `->first()`
 * menghasilkan satu model.
 *
 * Berkasnya dibaca sebagai teks, bukan dimuat - kelas di CQC_Phpstan memakai
 * sintaks PHP 8 sedangkan test ini berjalan di PHP 7.4, sama seperti alasan di
 * PhpstanExtensionRegistrationTest.
 */
class ModelRelationClassificationTest extends TestCase {
    /**
     * @return string
     */
    protected function extensionPath() {
        return dirname(__DIR__, 2) . '/system/libraries/CQC/Phpstan/Service/Property/ModelPropertyExtension.php';
    }

    /**
     * @return string
     */
    protected function relationDirectory() {
        return dirname(__DIR__, 2) . '/system/libraries/CModel/Relation';
    }

    /**
     * Isi daftar MANY_RELATION, dibaca sebagai teks.
     *
     * @return string[]
     */
    protected function manyRelationList() {
        $source = file_get_contents($this->extensionPath());

        $this->assertIsString($source, 'ModelPropertyExtension tidak terbaca');

        $matched = preg_match('/const MANY_RELATION = \[(.*?)\];/s', $source, $block);
        $this->assertSame(1, $matched, 'Blok const MANY_RELATION tidak ditemukan');

        preg_match_all('/([A-Za-z_][A-Za-z0-9_]*)::class/', $block[1], $classes);

        return $classes[1];
    }

    /**
     * Relasi yang punya getResults() sendiri, beserta klasifikasi menurut
     * isinya.
     *
     * @return array<string, bool> nama kelas => true bila hasilnya koleksi
     */
    protected function relationResultShape() {
        $shape = [];

        foreach (glob($this->relationDirectory() . '/*.php') as $file) {
            $source = file_get_contents($file);

            //hanya yang mendefinisikan getResults() sendiri; yang mewarisi
            //ikut klasifikasi induknya dan tidak perlu disebut dua kali
            if (preg_match('/function getResults\(\).*?\n    \}/s', $source, $body) !== 1) {
                continue;
            }

            $className = 'CModel_Relation_' . basename($file, '.php');
            $returnsCollection = strpos($body[0], 'newCollection') !== false
                || strpos($body[0], '->get()') !== false;

            $shape[$className] = $returnsCollection;
        }

        return $shape;
    }

    public function testEveryListedRelationClassExists() {
        foreach ($this->manyRelationList() as $className) {
            $file = $this->relationDirectory() . '/' . str_replace('CModel_Relation_', '', $className) . '.php';
            $this->assertFileExists($file, $className . ' terdaftar di MANY_RELATION tetapi berkasnya tidak ada');
        }
    }

    public function testRelationReturningCollectionIsListedAsMany() {
        $listed = $this->manyRelationList();
        $missing = [];

        foreach ($this->relationResultShape() as $className => $returnsCollection) {
            if ($returnsCollection && !in_array($className, $listed)) {
                $missing[] = $className;
            }
        }

        $this->assertSame(
            [],
            $missing,
            'getResults()-nya mengembalikan koleksi tetapi tidak ada di MANY_RELATION: ' . implode(', ', $missing)
        );
    }

    public function testRelationReturningSingleModelIsNotListedAsMany() {
        $listed = $this->manyRelationList();
        $wrong = [];

        foreach ($this->relationResultShape() as $className => $returnsCollection) {
            if (!$returnsCollection && in_array($className, $listed)) {
                $wrong[] = $className;
            }
        }

        $this->assertSame(
            [],
            $wrong,
            'getResults()-nya mengembalikan satu model tetapi terdaftar sebagai jamak: ' . implode(', ', $wrong)
        );
    }

    public function testThePrerequisiteOfThisTestStillHolds() {
        //kalau tidak ada satu pun relasi yang terbaca, ketiga test di atas
        //lulus tanpa memeriksa apa pun
        $shape = $this->relationResultShape();

        $this->assertGreaterThan(8, count($shape), 'Relasi yang terbaca terlalu sedikit - polanya berubah?');
        $this->assertContains(true, $shape, 'Tidak ada relasi jamak yang terbaca');
        $this->assertContains(false, $shape, 'Tidak ada relasi tunggal yang terbaca');
    }
}
