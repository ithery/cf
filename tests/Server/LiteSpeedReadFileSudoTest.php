<?php

use PHPUnit\Framework\TestCase;

/**
 * Penjagaan atas urutan pemeriksaan pada `CServer_WebServer_LiteSpeed::readFile()`.
 *
 * Berkas konfigurasi LiteSpeed lazimnya berada di direktori yang tidak dapat
 * ditembus pengguna biasa - `/usr/local/lsws/conf` ber-mode 0750 milik `lsadm`.
 * Pada keadaan itu `[ -e ]` bernilai salah meskipun berkasnya ada dan terbaca
 * lewat `sudo`.
 *
 * Selama `[ -e ]` menjadi penjaga bagi jalur sudo, berkas semacam itu
 * dilaporkan `missing`. Tidak ada galat yang terlempar, dan pemanggilnya
 * menyimpulkan server tidak punya virtual host sama sekali - itulah yang
 * membuatnya sulit terlihat, dan alasan penjagaan ini dilakukan atas sumbernya.
 */
class LiteSpeedReadFileSudoTest extends TestCase {
    /**
     * Isi readFile() saja, supaya penjagaan ini tidak lulus hanya karena ada
     * `sudo` di tempat lain pada berkas yang sama.
     *
     * @return string
     */
    protected function readFileBody() {
        $source = (string) file_get_contents(dirname(__DIR__, 2) . '/system/libraries/CServer/WebServer/LiteSpeed.php');

        $start = strpos($source, 'public function readFile(');
        $this->assertNotFalse($start, 'method readFile() tidak ditemukan');

        $end = strpos($source, 'public function writeFile(', $start);
        $this->assertNotFalse($end, 'akhir readFile() tidak ditemukan');

        return substr($source, $start, $end - $start);
    }

    /**
     * @return void
     */
    public function testSudoDicobaSebelumKeberadaanBerkasDipertanyakan() {
        $body = $this->readFileBody();

        $posSudoCat = strpos($body, 'sudo -n cat');
        //`elif [ -e `, bukan `[ -e ` polos: bentuk yang polos juga muncul pada
        //komentar yang menerangkan jebakan ini, dan mencocokkannya membuat test
        //ini memeriksa komentarnya alih-alih kodenya
        $posExists = strpos($body, 'elif [ -e ');

        $this->assertNotFalse($posSudoCat, 'jalur sudo hilang dari readFile()');
        $this->assertNotFalse($posExists, 'pemeriksaan keberadaan berkas hilang dari readFile()');
        $this->assertTrue(
            $posSudoCat < $posExists,
            'pemeriksaan `[ -e ]` kembali mendahului jalur sudo - berkas di direktori yang tidak dapat ditembus akan dilaporkan missing'
        );
    }

    /**
     * Pemeriksaan keberadaan berkas harus punya cadangan sudo juga, supaya
     * `denied` dan `missing` tidak tertukar pada direktori yang tertutup.
     *
     * @return void
     */
    public function testPemeriksaanKeberadaanPunyaCadanganSudo() {
        $this->assertTrue(
            strpos($this->readFileBody(), 'sudo -n test -e') !== false,
            'pemeriksaan keberadaan berkas kehilangan cadangan sudo - berkas yang tertutup akan dilaporkan missing, bukan denied'
        );
    }

    /**
     * Cadangan tetap berupa cadangan: pembacaan langsung didahulukan, sehingga
     * server tanpa sudo sama sekali tidak kehilangan kemampuan yang sudah ada.
     *
     * @return void
     */
    public function testPembacaanLangsungTetapDidahulukan() {
        $body = $this->readFileBody();

        $posDirect = strpos($body, '[ -r ');
        $posSudo = strpos($body, 'sudo -n cat');

        $this->assertNotFalse($posDirect, 'pembacaan langsung hilang dari readFile()');
        $this->assertTrue($posDirect < $posSudo, 'pembacaan langsung tidak lagi didahulukan');
    }

    /**
     * Ketiga keadaan yang dijanjikan readFile() harus tetap dapat dihasilkan.
     *
     * @return void
     */
    public function testKetigaKeadaanTetapAda() {
        $body = $this->readFileBody();

        foreach (['LSSTATE|ok', 'LSSTATE|denied', 'LSSTATE|missing'] as $state) {
            $this->assertTrue(
                strpos($body, $state) !== false,
                'keadaan ' . $state . ' tidak lagi dihasilkan readFile()'
            );
        }
    }
}
