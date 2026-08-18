<?php

use PHPUnit\Framework\TestCase;

/**
 * Penjagaan atas cadangan `sudo -n` saat memeriksa webserver.
 *
 * `CServer_WebServer::inspect()` menyusun satu skrip shell lalu menguraikan
 * keluarannya. Sebagian server hanya mengizinkan masuk sebagai pengguna biasa
 * yang ber-sudo, sementara `/usr/local/lsws/conf` lazimnya hanya milik root.
 * Tanpa cadangan sudo, `[ -d ... ]` bernilai salah dan barisnya tidak pernah
 * tercetak - vhost selalu NULL, port selalu kosong, tanpa satu pun galat.
 *
 * Itulah yang membuatnya sulit terlihat, dan itu pula alasan penjagaan ini
 * dilakukan atas sumbernya: kegagalannya tidak melempar apa-apa, jadi tidak ada
 * yang bisa ditangkap dari perilaku tanpa server sungguhan.
 */
class WebServerSudoFallbackTest extends TestCase {
    /**
     * @return string
     */
    protected function source() {
        return (string) file_get_contents(dirname(__DIR__, 2) . '/system/libraries/CServer/WebServer.php');
    }

    /**
     * Isi inspect() saja, supaya penjagaan ini tidak lulus hanya karena ada
     * `sudo` di tempat lain pada berkas yang sama.
     *
     * @return string
     */
    protected function inspectBody() {
        $source = $this->source();
        $start = strpos($source, 'public function inspect()');
        $this->assertNotFalse($start, 'method inspect() tidak ditemukan');

        $end = strpos($source, '$output = $this->run($script);', $start);
        $this->assertNotFalse($end, 'akhir penyusunan skrip pada inspect() tidak ditemukan');

        return substr($source, $start, $end - $start);
    }

    /**
     * @return void
     */
    public function testDirectoryChecksHaveSudoFallback() {
        $body = $this->inspectBody();

        $this->assertTrue(
            strpos($body, 'sudo -n test -d') !== false,
            'pemeriksaan direktori kehilangan cadangan sudo - vhost akan kembali NULL pada server yang tidak dimasuki sebagai root'
        );
        $this->assertTrue(
            strpos($body, 'sudo -n ls -1') !== false,
            'penghitungan isi direktori kehilangan cadangan sudo'
        );
    }

    /**
     * Titik panggil yang benar-benar rusak dahulu, bukan sekadar definisi
     * pembantunya.
     *
     * Tanpa pemeriksaan ini satu titik panggil bisa dikembalikan ke `[ -d ]`
     * polos sementara pembantunya tetap terdefinisi dan dipakai di tempat lain -
     * dan testnya lolos, padahal justru vhost LiteSpeed yang kembali NULL.
     *
     * @return void
     */
    public function testLiteSpeedConfigChecksUseTheHelpers() {
        $body = $this->inspectBody();

        $this->assertTrue(
            strpos($body, '_isdir /usr/local/lsws/conf/vhosts') !== false,
            'pemeriksaan direktori vhost LiteSpeed tidak memakai _isdir - vhost akan kembali NULL'
        );
        $this->assertTrue(
            strpos($body, '_count /usr/local/lsws/conf/vhosts') !== false,
            'penghitungan vhost LiteSpeed tidak memakai _count'
        );
        $this->assertTrue(
            strpos($body, '_isdir /usr/local/lsws/conf ') !== false,
            'pemeriksaan direktori konfigurasi LiteSpeed tidak memakai _isdir'
        );
    }

    /**
     * Nama proses pada `ss` hanya terlihat untuk proses milik sendiri,
     * sedangkan webserver berjalan sebagai root. Tanpa sudo kolomnya kosong,
     * dan port tidak pernah tercocokkan ke tipe webservernya.
     *
     * @return void
     */
    public function testPortListingHasSudoFallback() {
        $this->assertTrue(
            strpos($this->inspectBody(), 'sudo -n ss -lntp') !== false,
            'pembacaan port kehilangan cadangan sudo - port tidak akan tercocokkan ke tipe webserver'
        );
    }

    /**
     * Cadangan harus berupa cadangan, bukan pengganti: perintah langsung tetap
     * dicoba lebih dulu supaya server yang tidak punya sudo sama sekali tidak
     * kehilangan kemampuan yang sudah ada.
     *
     * @return void
     */
    public function testDirectAttemptStillComesFirst() {
        $body = $this->inspectBody();

        $this->assertTrue(
            strpos($body, '[ -d "$1" ] || sudo -n test -d') !== false,
            'pemeriksaan langsung tidak lagi didahulukan - server tanpa sudo bisa ikut kehilangan pembacaannya'
        );
        $this->assertTrue(
            strpos($body, '|| ss -lntp') !== false,
            'pembacaan port tidak lagi punya jalur tanpa sudo'
        );
    }

    /**
     * Pembantu shell-nya harus benar-benar dipakai, bukan didefinisikan lalu
     * dilupakan.
     *
     * @return void
     */
    public function testHelpersAreActuallyUsed() {
        $body = $this->inspectBody();

        foreach (['_isdir', '_isfile', '_count'] as $helper) {
            $this->assertTrue(
                substr_count($body, $helper) > 1,
                $helper . ' didefinisikan tetapi tidak dipakai di inspect()'
            );
        }
    }
}
