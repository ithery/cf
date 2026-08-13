<?php

use PHPUnit\Framework\TestCase;

/**
 * Menjaga agar ekstensi PHPStan di CQC_Phpstan benar-benar terpasang.
 *
 * PHPStan hanya memuat kelas yang terdaftar di blok `services` pada
 * phpstan.neon.dist. Sebuah ekstensi yang sudah ditulis lengkap tetapi lupa
 * didaftarkan akan mati tanpa jejak - tidak ada galat, tidak ada peringatan,
 * hanya analisis yang diam-diam lebih dangkal. Itu benar-benar terjadi pada
 * MacroMethodsClassReflectionExtension, yang selama itu membuat setiap method
 * hasil ::macro() dilaporkan sebagai method yang tidak ada.
 *
 * Test ini membaca berkasnya sebagai teks, bukan lewat container PHPStan,
 * supaya tetap berjalan di PHP 7.4 - kelas di dalam CQC_Phpstan sendiri memakai
 * sintaks PHP 8 karena ia perkakas pengembangan, jadi memuatnya akan fatal di
 * versi PHP yang ditargetkan framework.
 */
class PhpstanExtensionRegistrationTest extends TestCase {
    /**
     * Antarmuka yang menandai sebuah kelas sebagai ekstensi PHPStan, yakni
     * kelas yang tidak berguna sampai ia terdaftar.
     *
     * @var string[]
     */
    protected static $extensionInterfaces = [
        'MethodsClassReflectionExtension',
        'PropertiesClassReflectionExtension',
        'DynamicMethodReturnTypeExtension',
        'DynamicStaticMethodReturnTypeExtension',
        'TypeNodeResolverExtension',
        'StubFilesExtension',
        'Rule',
    ];

    /**
     * Ekstensi yang sengaja TIDAK didaftarkan, beserta alasannya.
     *
     * Daftar ini bukan tempat menampung yang belum sempat dikerjakan. Setiap
     * baris di sini adalah keputusan, dan alasannya juga ditulis sebagai
     * komentar di phpstan.neon.dist supaya terbaca oleh orang yang hendak
     * "memperbaiki" ketiadaannya.
     *
     * @var array<string, string>
     */
    protected static $intentionallyUnregistered = [
        'CQC_Phpstan_Service_Rule_NoModelMakeRule' => 'acuannya Model::class milik Laravel yang tidak ada di CF, dan make() di CF adalah pabrik dari nama model - bukan Model::make(array)',
    ];

    /**
     * @return string
     */
    protected function docroot() {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    protected function neonPath() {
        return $this->docroot() . 'phpstan.neon.dist';
    }

    /**
     * Semua kelas di CQC_Phpstan yang mengimplementasikan antarmuka ekstensi.
     *
     * @return string[]
     */
    protected function extensionClasses() {
        $base = $this->docroot() . 'system/libraries/CQC/Phpstan';
        $found = [];

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base));
        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $source = (string) file_get_contents($file->getPathname());
            if (!preg_match('/^(?:final\s+)?class\s+(CQC_Phpstan_[A-Za-z_]+)/m', $source, $classMatch)) {
                continue;
            }
            if (!preg_match('/\bimplements\s+([A-Za-z_,\s\\\\]+?)\s*\{/', $source, $implementsMatch)) {
                continue;
            }

            foreach (explode(',', $implementsMatch[1]) as $interface) {
                $interface = ltrim(trim($interface), '\\');
                if (in_array($interface, static::$extensionInterfaces, true)) {
                    $found[] = $classMatch[1];

                    break;
                }
            }
        }

        sort($found);

        return $found;
    }

    /**
     * Kelas yang terdaftar di blok `services`.
     *
     * Sengaja hanya membaca bagian setelah `services:` - sebuah nama kelas yang
     * disebut di komentar penjelasan tidak boleh dihitung sebagai terdaftar.
     *
     * @return string[]
     */
    protected function registeredClasses() {
        $neon = (string) file_get_contents($this->neonPath());
        $position = strpos($neon, 'services:');
        $services = $position === false ? '' : substr($neon, $position);

        //baris yang diawali '#' adalah komentar - dibuang supaya kelas yang
        //dijelaskan di sana tidak salah terbaca sebagai terdaftar
        $services = preg_replace('/^\s*#.*$/m', '', $services);

        preg_match_all('/class:\s*(CQC_Phpstan_[A-Za-z_]+)/', (string) $services, $matches);

        return array_values(array_unique($matches[1]));
    }

    /**
     * @return void
     */
    public function testExtensionsAreDiscovered() {
        $this->assertNotEmpty(
            $this->extensionClasses(),
            'tidak satu pun ekstensi CQC_Phpstan terbaca - pemindaiannya yang rusak, bukan kodenya'
        );
    }

    /**
     * @return void
     */
    public function testEveryExtensionIsRegisteredOrIntentionallyExcluded() {
        $registered = $this->registeredClasses();
        $missing = [];

        foreach ($this->extensionClasses() as $class) {
            if (in_array($class, $registered, true)) {
                continue;
            }
            if (array_key_exists($class, static::$intentionallyUnregistered)) {
                continue;
            }
            $missing[] = $class;
        }

        $this->assertSame(
            [],
            $missing,
            "ekstensi PHPStan berikut tidak terdaftar di phpstan.neon.dist, jadi tidak pernah dimuat:\n  "
                . implode("\n  ", $missing)
                . "\n\nDaftarkan di blok services, atau - kalau memang sengaja - tambahkan ke "
                . '$intentionallyUnregistered beserta alasannya.'
        );
    }

    /**
     * Yang sengaja dikecualikan harus benar-benar ada dan benar-benar tidak
     * terdaftar. Kalau tidak, daftarnya hanya menyimpan keputusan basi.
     *
     * @return void
     */
    public function testIntentionallyUnregisteredListStaysHonest() {
        $extensions = $this->extensionClasses();
        $registered = $this->registeredClasses();

        foreach (static::$intentionallyUnregistered as $class => $reason) {
            $this->assertContains(
                $class,
                $extensions,
                $class . ' tercatat sengaja tidak didaftarkan, tetapi kelasnya sudah tidak ada - buang barisnya'
            );
            $this->assertNotContains(
                $class,
                $registered,
                $class . ' tercatat sengaja tidak didaftarkan, tetapi ternyata terdaftar - buang barisnya'
            );
            $this->assertNotSame('', trim($reason), $class . ' dikecualikan tanpa alasan tertulis');
        }
    }

    /**
     * Alasan pengecualian harus juga ada di phpstan.neon.dist. Orang yang
     * hendak menambahkan ekstensinya membuka berkas itu, bukan berkas test ini.
     *
     * @return void
     */
    public function testExclusionReasonIsAlsoDocumentedInConfig() {
        $neon = (string) file_get_contents($this->neonPath());

        foreach (array_keys(static::$intentionallyUnregistered) as $class) {
            $this->assertStringContainsString(
                $class,
                $neon,
                $class . ' tidak disebut di phpstan.neon.dist - tanpa itu, orang berikutnya akan mendaftarkannya lagi'
            );
        }
    }
}
