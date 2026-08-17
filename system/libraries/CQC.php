<?php

defined('SYSPATH') or die('No direct access allowed.');

class CQC {
    const TYPE_DATABASE_CHECKER = 'DatabaseChecker';

    const TYPE_UNIT_TEST = 'UnitTest';

    public static function boot() {
        CQC_Bootstrap::boot();
    }

    /**
     * @param string $class
     * @param string $name
     * @param string $group
     *
     * @return void
     */
    public static function registerDatabaseChecker($class, $name = null, $group = null) {
        return CQC_Manager::instance()->registerDatabaseChecker($class, $name, $group);
    }

    /**
     * @param string $class
     * @param string $name
     * @param string $group
     *
     * @return void
     */
    public static function registerUnitTest($class, $name = null, $group = null) {
        return CQC_Manager::instance()->registerUnitTest($class, $name, $group);
    }

    /**
     * @param string $className
     *
     * @return \CQC_Runner_DatabaseCheckerRunner
     */
    public static function createDatabaseCheckerRunner($className) {
        return new CQC_Runner_DatabaseCheckerRunner($className);
    }

    /**
     * @param string $className
     *
     * @return \CQC_Runner_UnitTestRunner
     */
    public static function createUnitTestRunner($className) {
        return new CQC_Runner_UnitTestRunner($className);
    }

    /**
     * @param string $className
     *
     * @return \CQC_Inspector
     */
    public static function createInspector($className) {
        return new CQC_Inspector($className);
    }

    /**
     * @param string $className
     *
     * @return mixed
     */
    public static function createProcessor($className) {
        $inspector = new CQC_Inspector($className);

        return $inspector->createProcessor();
    }

    public static function cliRunner($className, $parameter = null) {
        $argv = carr::get($_SERVER, 'argv');
        if ($parameter == null) {
            $parameter = $argv[3];
        }
        parse_str($parameter, $options);
        $processor = static::createProcessor($className);
        $processor->run($options);
    }

    /**
     * @return CQC_Manager
     */
    public static function manager() {
        return CQC_Manager::instance();
    }

    /**
     * @return CQC_Executor
     */
    public static function createExecutor() {
        return new CQC_Executor();
    }

    /**
     * @return CQC_Phpstan
     */
    public static function phpstan() {
        return CQC_Phpstan::instance();
    }

    /**
     * @return CQC_Phpcs
     */
    public static function phpcs() {
        return CQC_Phpcs::instance();
    }

    /**
     * @return CQC_Phpcsfixer
     */
    public static function phpcsfixer() {
        return CQC_Phpcsfixer::instance();
    }

    /**
     * Nomor versi sebuah phar, null bila tidak terbaca.
     *
     * Versinya tidak tersimpan di mana pun selain di dalam phar itu sendiri,
     * jadi phar-nya dijalankan. Phar yang menolak versi PHP yang memanggilnya
     * juga tidak terbaca di sini - dan itu memang jawaban yang benar, sebab
     * yang seperti itu harus diganti.
     *
     * @param string $pharPath
     *
     * @return null|string
     */
    public static function pharVersion($pharPath) {
        if (!file_exists($pharPath)) {
            return null;
        }

        $php = (new \Symfony\Component\Process\PhpExecutableFinder())->find();
        if ($php === false) {
            return null;
        }

        $process = new \Symfony\Component\Process\Process([$php, $pharPath, '--version']);
        $process->setTimeout(60);

        try {
            $process->run();
        } catch (Exception $ex) {
            return null;
        }

        if (!$process->isSuccessful()) {
            return null;
        }

        if (!preg_match('/(\d+\.\d+\.\d+)/', $process->getOutput(), $matches)) {
            return null;
        }

        return $matches[1];
    }

    /**
     * Mengunduh phar, lalu memasangnya hanya bila versinya sesuai.
     *
     * Diunduh ke berkas sementara dulu: mengunduh langsung ke tujuan akan
     * mengosongkan phar yang masih bekerja begitu jaringannya putus di tengah.
     *
     * @param string $url
     * @param string $pharPath
     * @param string $expectedVersion
     *
     * @throws Exception
     *
     * @return void
     */
    public static function downloadPhar($url, $pharPath, $expectedVersion) {
        $temporaryPath = $pharPath . '.download';

        if (!CFile::isDirectory(dirname($pharPath))) {
            CFile::makeDirectory(dirname($pharPath), 0755, true);
        }

        try {
            if (!@copy($url, $temporaryPath)) {
                throw new Exception('Failed to download ' . $url);
            }

            $downloaded = static::pharVersion($temporaryPath);
            if ($downloaded !== $expectedVersion) {
                throw new Exception(
                    'Downloaded phar reports ' . ($downloaded == null ? 'no readable version' : $downloaded)
                    . ', expected ' . $expectedVersion
                );
            }

            CFile::move($temporaryPath, $pharPath);
            @chmod($pharPath, 0755);
        } catch (Exception $ex) {
            @unlink($temporaryPath);

            throw $ex;
        }
    }
}
