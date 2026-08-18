<?php

use PHPUnit\Framework\TestCase;

class SessionHandlerTest extends TestCase {
    protected function tearDown() {
        CCarbon::setTestNow();
    }

    /**
     * @return string
     */
    protected function sessionId() {
        return str_repeat('a', 40);
    }

    public function testTheArrayHandlerReadsBackWhatItWrote() {
        $handler = new CSession_Handler_ArraySessionHandler(10);
        $handler->write($this->sessionId(), 'muatan');

        $this->assertSame('muatan', $handler->read($this->sessionId()));
    }

    public function testTheArrayHandlerGivesAnEmptyStringForAnUnknownId() {
        $handler = new CSession_Handler_ArraySessionHandler(10);

        $this->assertSame('', $handler->read('tidak-ada'));
    }

    /**
     * Sesi yang lewat umurnya dibaca sebagai kosong, bukan sebagai data lama --
     * kalau tidak, sesi kedaluwarsa tetap dapat dipakai.
     */
    public function testTheArrayHandlerStopsReturningExpiredData() {
        $handler = new CSession_Handler_ArraySessionHandler(10);
        $handler->write($this->sessionId(), 'muatan');

        CCarbon::setTestNow(CCarbon::now()->addMinutes(11));

        $this->assertSame('', $handler->read($this->sessionId()));
    }

    public function testTheArrayHandlerKeepsDataThatIsStillYoungEnough() {
        $handler = new CSession_Handler_ArraySessionHandler(10);
        $handler->write($this->sessionId(), 'muatan');

        CCarbon::setTestNow(CCarbon::now()->addMinutes(9));

        $this->assertSame('muatan', $handler->read($this->sessionId()));
    }

    public function testTheArrayHandlerDestroysOneSession() {
        $handler = new CSession_Handler_ArraySessionHandler(10);
        $handler->write($this->sessionId(), 'muatan');
        $handler->destroy($this->sessionId());

        $this->assertSame('', $handler->read($this->sessionId()));
    }

    public function testDestroyingAnUnknownSessionIsHarmless() {
        $handler = new CSession_Handler_ArraySessionHandler(10);

        $this->assertTrue($handler->destroy('tidak-ada'));
    }

    /**
     * gc() membuang yang lebih tua dari umur yang diminta dan menyisakan sisanya
     * -- ia dipanggil berkala, jadi salah batas berarti sesi yang masih dipakai
     * ikut terbuang.
     */
    public function testGarbageCollectionOnlyRemovesWhatIsOlderThanTheLifetime() {
        $handler = new CSession_Handler_ArraySessionHandler(10);
        $handler->write('lama', 'a');

        CCarbon::setTestNow(CCarbon::now()->addMinutes(5));
        $handler->write('baru', 'b');

        $handler->gc(60);

        $this->assertSame('', $handler->read('lama'));
        $this->assertSame('b', $handler->read('baru'));
    }

    public function testOpenAndCloseAlwaysSucceed() {
        $handler = new CSession_Handler_ArraySessionHandler(10);

        $this->assertTrue($handler->open('', 'nama'));
        $this->assertTrue($handler->close());
    }

    /**
     * Penangan null menerima semuanya dan tidak menyimpan apa pun; ia dipakai
     * ketika sesi sengaja dimatikan, jadi tidak boleh ada yang melempar.
     */
    public function testTheNullHandlerNeverStoresAnything() {
        $handler = new CSession_Handler_NullSessionHandler();

        $this->assertTrue($handler->open('', 'nama'));
        $this->assertTrue($handler->write($this->sessionId(), 'muatan'));
        $this->assertSame('', $handler->read($this->sessionId()));
        $this->assertTrue($handler->destroy($this->sessionId()));
        $this->assertTrue($handler->gc(60));
        $this->assertTrue($handler->close());
    }

    public function testTheCacheHandlerStoresThroughTheCache() {
        $cache = CCache::manager()->driver('array');
        $handler = new CSession_Handler_CacheBasedSessionHandler($cache, 600);
        $handler->write($this->sessionId(), 'muatan');

        $this->assertSame('muatan', $handler->read($this->sessionId()));
        $this->assertSame($cache, $handler->getCache());
    }

    public function testTheCacheHandlerForgetsWhatItDestroys() {
        $handler = new CSession_Handler_CacheBasedSessionHandler(CCache::manager()->driver('array'), 600);
        $handler->write($this->sessionId(), 'muatan');
        $handler->destroy($this->sessionId());

        $this->assertSame('', $handler->read($this->sessionId()));
    }

    /**
     * Sebuah sesi berjalan utuh melewati penangan: dimulai, diisi, disimpan,
     * lalu dibaca lagi oleh contoh baru dengan id yang sama.
     */
    public function testAStoreRoundTripsThroughTheCacheHandler() {
        $handler = new CSession_Handler_CacheBasedSessionHandler(CCache::manager()->driver('array'), 600);
        $id = str_repeat('b', 40);

        $session = new CSession_Store('nama_sesi', $handler, $id);
        $session->start();
        $session->put('nama', 'nilai');
        $session->save();

        $lagi = new CSession_Store('nama_sesi', $handler, $id);
        $lagi->start();

        $this->assertSame('nilai', $lagi->get('nama'));
    }
}
