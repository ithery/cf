<?php

use PHPUnit\Framework\TestCase;

/**
 * Server tiruan: mencatat perintah yang diminta dan menjawab dengan keluaran
 * yang sudah disiapkan, tanpa menyentuh SSH.
 */
class StubRemoteServer extends CServer_Server {
    /**
     * @var string
     */
    public $reply;

    /**
     * @var string[]
     */
    public $commands = [];

    /**
     * @param string $reply
     */
    public function __construct($reply = '') {
        parent::__construct();
        $this->reply = $reply;
    }

    public function isRemote() {
        return true;
    }

    public function isLocal() {
        return false;
    }

    public function runCommand($command) {
        $this->commands[] = $command;

        return $this->reply;
    }
}

/**
 * `php` telanjang tidak dapat diandalkan di server remote: PATH pengguna SSH
 * lazim hanya `/usr/local/bin:/usr/bin`, sedangkan PHP-nya berada di
 * `/usr/local/lsws/lsphpXX/bin/php`.
 *
 * Gejalanya menyesatkan dan itulah yang membuatnya perlu dijaga: perintahnya
 * tidak melempar apa pun, hanya menghasilkan keluaran kosong - sehingga
 * pemanggil yang mengurai keluarannya melapor gagal membaca berkas, bukan
 * gagal menemukan PHP. Terlihat pada server3.tribelio.com, 15 Agustus 2026.
 */
class PhpBinaryDetectionTest extends TestCase {
    /**
     * @return void
     */
    public function testAnExplicitBinaryIsUsedAsGivenWithoutAsking() {
        $server = new StubRemoteServer('/usr/local/lsws/lsphp74/bin/php');
        $php = new CServer_Php($server, '/opt/php8/bin/php');

        $this->assertSame('/opt/php8/bin/php', $php->getPhpBinary());
        $this->assertSame([], $server->commands, 'biner yang sudah disebutkan tidak boleh memicu pencarian');
    }

    /**
     * @return void
     */
    public function testWithoutAnExplicitBinaryItAsksTheServer() {
        $server = new StubRemoteServer('/usr/local/lsws/lsphp74/bin/php');
        $php = new CServer_Php($server);

        $this->assertSame('/usr/local/lsws/lsphp74/bin/php', $php->getPhpBinary());
        $this->assertCount(1, $server->commands);
    }

    /**
     * Pencariannya sekali saja - perjalanan SSH mahal, dan biner tidak berpindah
     * di tengah jalan.
     *
     * @return void
     */
    public function testDetectionHappensOnlyOnce() {
        $server = new StubRemoteServer('/usr/local/lsws/lsphp74/bin/php');
        $php = new CServer_Php($server);

        $php->getPhpBinary();
        $php->getPhpBinary();
        $php->getPhpBinary();

        $this->assertCount(1, $server->commands, 'pencarian terulang - tiap pemanggilan menambah satu perjalanan SSH');
    }

    /**
     * @return void
     */
    public function testTheLookupPrefersPathThenFallsBackToLiteSpeed() {
        $server = new StubRemoteServer('/usr/bin/php');
        (new CServer_Php($server))->getPhpBinary();

        $command = $server->commands[0];

        $this->assertStringContainsString('command -v php', $command);
        $this->assertStringContainsString('/usr/local/lsws/lsphp*/bin/php', $command);
        $this->assertTrue(
            strpos($command, 'command -v php') < strpos($command, 'lsphp'),
            'PATH seharusnya diperiksa lebih dulu sebelum jatuh ke LiteSpeed'
        );
    }

    /**
     * Beberapa versi lsphp lazim terpasang berdampingan; yang tertinggi yang
     * dipakai, dan itu ditentukan oleh urutan menurun pada perintahnya.
     *
     * @return void
     */
    public function testTheLookupSortsLiteSpeedVersionsDescending() {
        $server = new StubRemoteServer('/usr/local/lsws/lsphp74/bin/php');
        (new CServer_Php($server))->getPhpBinary();

        $this->assertStringContainsString('sort -Vr', $server->commands[0]);
    }

    /**
     * @return void
     */
    public function testOnlyTheFirstLineOfTheReplyIsTaken() {
        $server = new StubRemoteServer("/usr/local/lsws/lsphp74/bin/php\n/usr/local/lsws/lsphp56/bin/php");

        $this->assertSame('/usr/local/lsws/lsphp74/bin/php', (new CServer_Php($server))->getPhpBinary());
    }

    /**
     * Bila tidak ada yang ketemu sama sekali, kembali ke `php` — perilaku
     * sebelumnya — supaya server yang selama ini bekerja tidak ikut berubah.
     *
     * @return void
     */
    public function testAnEmptyReplyFallsBackToPlainPhp() {
        $this->assertSame('php', (new CServer_Php(new StubRemoteServer('')))->getPhpBinary());
        $this->assertSame('php', (new CServer_Php(new StubRemoteServer("  \n ")))->getPhpBinary());
    }

    /**
     * @return void
     */
    public function testALocalServerNeverAsksAndJustUsesPhp() {
        $local = new CServer_Server();
        $php = new CServer_Php($local);

        $this->assertTrue($local->isLocal());
        $this->assertSame('php', $php->getPhpBinary());
    }
}
