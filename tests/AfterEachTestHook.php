<?php

use PHPUnit\Runner\Hook\AfterTestHook;

/**
 * Mengembalikan keadaan global sesudah setiap test.
 *
 * Jam beku dan zona waktu itu keadaan proses, bukan milik satu test. Sebuah
 * test yang memajukan CCarbon lalu selesai akan meninggalkan jamnya beku untuk
 * test berikutnya, dan kegagalannya muncul di berkas yang tidak bersalah.
 */
class AfterEachTestHook implements AfterTestHook {
    /**
     * @var string
     */
    private $timezone;

    public function __construct() {
        $this->timezone = date_default_timezone_get();
    }

    /**
     * @param string $test
     * @param float  $time
     *
     * @return void
     */
    public function executeAfterTest($test, $time) {
        if (class_exists('CCarbon', false)) {
            CCarbon::setTestNow();
        }

        date_default_timezone_set($this->timezone);
    }
}
