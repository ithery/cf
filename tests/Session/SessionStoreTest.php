<?php

use PHPUnit\Framework\TestCase;

class SessionStoreTest extends TestCase {
    /**
     * @param null|string $id
     *
     * @return CSession_Store
     */
    protected function makeStore($id = null) {
        return new CSession_Store('nama_sesi', new CSession_Handler_ArraySessionHandler(10), $id);
    }

    public function testAStartedSessionIsMarkedAsStarted() {
        $session = $this->makeStore();

        $this->assertFalse($session->isStarted());
        $this->assertTrue($session->start());
        $this->assertTrue($session->isStarted());
    }

    /**
     * Token CSRF dibuat saat sesi dimulai; tanpa itu setiap borang yang dikirim
     * ditolak karena tidak ada yang dapat dicocokkan.
     */
    public function testStartingCreatesACsrfToken() {
        $session = $this->makeStore();
        $session->start();

        $this->assertSame(40, strlen($session->token()));
        $this->assertSame($session->token(), $session->get('_token'));
    }

    public function testAnIdIsGeneratedWhenNoneIsGiven() {
        $session = $this->makeStore();

        $this->assertSame(40, strlen($session->getId()));
        $this->assertTrue($session->isValidId($session->getId()));
    }

    public function testTheGivenIdIsKeptWhenItIsValid() {
        $id = str_repeat('a', 40);

        $this->assertSame($id, $this->makeStore($id)->getId());
    }

    /**
     * Id yang tidak berbentuk benar dibuang dan diganti yang baru -- itu yang
     * menahan id sesi kiriman penyerang dari luar.
     */
    public function testAMalformedIdIsReplaced() {
        $session = $this->makeStore('terlalu-pendek');

        $this->assertNotSame('terlalu-pendek', $session->getId());
        $this->assertSame(40, strlen($session->getId()));
    }

    public function testPutAndGetCarryTheValue() {
        $session = $this->makeStore();
        $session->put('nama', 'nilai');

        $this->assertSame('nilai', $session->get('nama'));
    }

    public function testGetFallsBackToTheDefault() {
        $this->assertSame('bawaan', $this->makeStore()->get('tidak.ada', 'bawaan'));
    }

    public function testPutAcceptsAnArrayOfPairs() {
        $session = $this->makeStore();
        $session->put(['satu' => 'a', 'dua' => 'b']);

        $this->assertSame('a', $session->get('satu'));
        $this->assertSame('b', $session->get('dua'));
    }

    /**
     * Kunci bertitik menembus larik bersarang, sehingga sepotong keadaan dapat
     * dibaca tanpa mengambil seluruh cabangnya.
     */
    public function testDottedKeysReachIntoNestedArrays() {
        $session = $this->makeStore();
        $session->put('pengguna', ['profil' => ['nama' => 'Hery']]);

        $this->assertSame('Hery', $session->get('pengguna.profil.nama'));
    }

    public function testHasIsFalseForANullValueButExistsIsTrue() {
        $session = $this->makeStore();
        $session->put('kosong', null);

        $this->assertFalse($session->has('kosong'));
        $this->assertTrue($session->exists('kosong'));
        $this->assertFalse($session->missing('kosong'));
    }

    public function testPullReturnsTheValueAndRemovesIt() {
        $session = $this->makeStore();
        $session->put('nama', 'nilai');

        $this->assertSame('nilai', $session->pull('nama'));
        $this->assertFalse($session->exists('nama'));
    }

    public function testRememberOnlyComputesTheValueOnce() {
        $session = $this->makeStore();
        $dihitung = 0;
        $callback = function () use (&$dihitung) {
            $dihitung++;

            return 'nilai';
        };

        $this->assertSame('nilai', $session->remember('kunci', $callback));
        $this->assertSame('nilai', $session->remember('kunci', $callback));
        $this->assertSame(1, $dihitung);
    }

    public function testPushAppendsToAnArrayValue() {
        $session = $this->makeStore();
        $session->push('daftar', 'a');
        $session->push('daftar', 'b');

        $this->assertSame(['a', 'b'], $session->get('daftar'));
    }

    public function testIncrementAndDecrementWorkOnAMissingKey() {
        $session = $this->makeStore();

        $this->assertSame(1, $session->increment('hitung'));
        $this->assertSame(4, $session->increment('hitung', 3));
        $this->assertSame(3, $session->decrement('hitung'));
    }

    public function testOnlyNarrowsTheDataToTheGivenKeys() {
        $session = $this->makeStore();
        $session->put(['satu' => 'a', 'dua' => 'b', 'tiga' => 'c']);

        $this->assertSame(['satu' => 'a', 'tiga' => 'c'], $session->only(['satu', 'tiga']));
    }

    public function testForgetRemovesOnlyWhatWasNamed() {
        $session = $this->makeStore();
        $session->put(['satu' => 'a', 'dua' => 'b']);
        $session->forget('satu');

        $this->assertFalse($session->exists('satu'));
        $this->assertTrue($session->exists('dua'));
    }

    public function testFlushEmptiesEverything() {
        $session = $this->makeStore();
        $session->put(['satu' => 'a', 'dua' => 'b']);
        $session->flush();

        $this->assertSame([], $session->all());
    }

    /**
     * Data kilat bertahan tepat satu permintaan berikutnya: itulah yang membuat
     * pesan "berhasil disimpan" muncul sekali sesudah pengalihan, lalu hilang.
     */
    public function testFlashDataSurvivesExactlyOneMoreRequest() {
        $session = $this->makeStore();
        $session->flash('pesan', 'berhasil');

        $this->assertSame('berhasil', $session->get('pesan'));

        $session->ageFlashData();
        $this->assertSame('berhasil', $session->get('pesan'));

        $session->ageFlashData();
        $this->assertNull($session->get('pesan'));
    }

    public function testNowIsReadableInThisRequestOnly() {
        $session = $this->makeStore();
        $session->now('pesan', 'berhasil');

        $this->assertSame('berhasil', $session->get('pesan'));

        $session->ageFlashData();
        $this->assertNull($session->get('pesan'));
    }

    public function testReflashKeepsTheFlashDataOneRoundLonger() {
        $session = $this->makeStore();
        $session->flash('pesan', 'berhasil');
        $session->ageFlashData();
        $session->reflash();
        $session->ageFlashData();

        $this->assertSame('berhasil', $session->get('pesan'));
    }

    public function testKeepOnlyHoldsOnToTheNamedKeys() {
        $session = $this->makeStore();
        $session->flash('tetap', 'a');
        $session->flash('lepas', 'b');
        $session->ageFlashData();
        $session->keep(['tetap']);
        $session->ageFlashData();

        $this->assertSame('a', $session->get('tetap'));
        $this->assertNull($session->get('lepas'));
    }

    /**
     * Masukan lama disimpan agar borang yang gagal validasi dapat diisi ulang
     * dengan apa yang tadi diketik pengguna.
     */
    public function testOldInputIsReadableAfterFlashing() {
        $session = $this->makeStore();
        $session->flashInput(['nama' => 'Hery', 'umur' => null]);

        $this->assertTrue($session->hasOldInput());
        $this->assertTrue($session->hasOldInput('nama'));
        $this->assertFalse($session->hasOldInput('umur'));
        $this->assertSame('Hery', $session->getOldInput('nama'));
        $this->assertSame('bawaan', $session->getOldInput('tidak.ada', 'bawaan'));
    }

    public function testTheDataSurvivesASaveAndReload() {
        $handler = new CSession_Handler_ArraySessionHandler(10);
        $id = str_repeat('a', 40);

        $session = new CSession_Store('nama_sesi', $handler, $id);
        $session->start();
        $session->put('nama', 'nilai');
        $session->save();

        $lagi = new CSession_Store('nama_sesi', $handler, $id);
        $lagi->start();

        $this->assertSame('nilai', $lagi->get('nama'));
    }

    public function testSavingMarksTheSessionAsNoLongerStarted() {
        $session = $this->makeStore();
        $session->start();
        $session->save();

        $this->assertFalse($session->isStarted());
    }

    /**
     * Regenerasi id itu penangkal session fixation: id berganti sementara isinya
     * tetap, sehingga id yang sudah diketahui penyerang tidak lagi berlaku.
     */
    public function testRegenerateChangesTheIdButKeepsTheData() {
        $session = $this->makeStore();
        $session->start();
        $session->put('nama', 'nilai');
        $lama = $session->getId();

        $session->regenerate();

        $this->assertNotSame($lama, $session->getId());
        $this->assertSame('nilai', $session->get('nama'));
    }

    public function testInvalidateChangesTheIdAndEmptiesTheData() {
        $session = $this->makeStore();
        $session->start();
        $session->put('nama', 'nilai');
        $lama = $session->getId();

        $session->invalidate();

        $this->assertNotSame($lama, $session->getId());
        $this->assertSame([], $session->all());
    }

    public function testRegenerateTokenReplacesTheCsrfToken() {
        $session = $this->makeStore();
        $session->start();
        $lama = $session->token();

        $session->regenerateToken();

        $this->assertNotSame($lama, $session->token());
    }

    public function testThePreviousUrlIsRememberedUnderItsOwnKey() {
        $session = $this->makeStore();
        $session->setPreviousUrl('https://contoh.test/sebelumnya');

        $this->assertSame('https://contoh.test/sebelumnya', $session->previousUrl());
        $this->assertSame('https://contoh.test/sebelumnya', $session->get('_previous.url'));
    }

    public function testTheNameCanBeReadAndReplaced() {
        $session = $this->makeStore();

        $this->assertSame('nama_sesi', $session->getName());
        $session->setName('lain');
        $this->assertSame('lain', $session->getName());
    }

    public function testGetHandlerHandsBackTheHandler() {
        $handler = new CSession_Handler_ArraySessionHandler(10);
        $session = new CSession_Store('nama_sesi', $handler);

        $this->assertSame($handler, $session->getHandler());
        $this->assertFalse($session->handlerNeedsRequest());
    }
}
