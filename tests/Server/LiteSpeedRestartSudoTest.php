<?php

use PHPUnit\Framework\TestCase;

/**
 * Penjagaan atas urutan percobaan pada `CServer_WebServer_LiteSpeed::restart()`.
 *
 * `lswsctrl` milik root, dan sebagai pengguna biasa yang ber-sudo ia tidak
 * gagal bersih: ia menempuh separuh jalan lebih dulu - gagal menulis
 * `logs/lsrestart.log`, gagal `pkill` proses milik root, melaporkan "Can't
 * determine the Home of LiteSpeed Web Server", lalu "[ERROR] Failed to start
 * litespeed!".
 *
 * Selama percobaan tanpa sudo didahulukan, seluruh rangkaian itu sudah tercetak
 * sebelum jalur sudo sempat menyala. Webserver-nya pada akhirnya memang menyala,
 * tetapi keluarannya terbaca seperti kegagalan - dan itu jenis laporan yang
 * membuat orang mengira situsnya mati.
 */
class LiteSpeedRestartSudoTest extends TestCase {
    /**
     * Isi restart() saja, supaya penjagaan ini tidak lulus hanya karena ada
     * `sudo` di tempat lain pada berkas yang sama.
     *
     * @return string
     */
    protected function restartBody() {
        $source = (string) file_get_contents(dirname(__DIR__, 2) . '/system/libraries/CServer/WebServer/LiteSpeed.php');

        $start = strpos($source, 'public function restart()');
        $this->assertNotFalse($start, 'method restart() tidak ditemukan');

        $end = strpos($source, 'public function ', $start + 10);
        $this->assertNotFalse($end, 'akhir restart() tidak ditemukan');

        return substr($source, $start, $end - $start);
    }

    /**
     * @return void
     */
    public function testSudoIsAttemptedBeforeThePlainCommand() {
        $body = $this->restartBody();

        $posSudo = strpos($body, "'sudo -n '");
        $posPlain = strpos($body, "' || '");

        $this->assertNotFalse($posSudo, 'jalur sudo hilang dari restart()');
        $this->assertNotFalse($posPlain, 'cadangan tanpa sudo hilang dari restart()');
        $this->assertTrue(
            $posSudo < $posPlain,
            'percobaan tanpa sudo kembali didahulukan - keluarannya akan penuh galat izin sebelum jalur sudo menyala'
        );
    }

    /**
     * Cadangan tetap ada, supaya server yang tidak mengenal sudo sama sekali
     * tidak kehilangan kemampuan yang sudah dimilikinya.
     *
     * @return void
     */
    public function testThePlainCommandRemainsAsAFallback() {
        $this->assertSame(
            2,
            substr_count($this->restartBody(), 'lswsctrl'),
            'restart() seharusnya tetap punya dua percobaan: dengan sudo dan tanpa sudo'
        );
    }

    /**
     * @return void
     */
    public function testOutputIsStillCaptured() {
        $this->assertSame(
            2,
            substr_count($this->restartBody(), '2>&1'),
            'keluaran galat tidak lagi ikut ditangkap, padahal itu satu-satunya petunjuk saat gagal'
        );
    }
}
