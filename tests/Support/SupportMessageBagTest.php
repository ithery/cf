<?php

use PHPUnit\Framework\TestCase;

class SupportMessageBagTest extends TestCase {
    public function testANewBagIsEmpty() {
        $bag = new CBase_MessageBag();

        $this->assertTrue($bag->isEmpty());
        $this->assertFalse($bag->isNotEmpty());
        $this->assertFalse($bag->any());
        $this->assertSame(0, $bag->count());
        $this->assertSame([], $bag->all());
    }

    public function testTheBagCanBeSeededFromTheConstructor() {
        $bag = new CBase_MessageBag(['nama' => ['wajib diisi']]);

        $this->assertTrue($bag->has('nama'));
        $this->assertSame(['wajib diisi'], $bag->get('nama'));
    }

    public function testAddCollectsSeveralMessagesUnderOneKey() {
        $bag = new CBase_MessageBag();
        $bag->add('nama', 'wajib diisi');
        $bag->add('nama', 'terlalu pendek');

        $this->assertSame(['wajib diisi', 'terlalu pendek'], $bag->get('nama'));
        $this->assertSame(2, $bag->count());
    }

    /**
     * Pesan yang sama persis untuk kunci yang sama tidak digandakan -- kalau
     * tidak, satu aturan yang diperiksa dua kali menampilkan pesan kembar.
     */
    public function testTheSameMessageIsNotAddedTwice() {
        $bag = new CBase_MessageBag();
        $bag->add('nama', 'wajib diisi');
        $bag->add('nama', 'wajib diisi');

        $this->assertSame(['wajib diisi'], $bag->get('nama'));
    }

    public function testHasIsFalseForAKeyThatWasNeverAdded() {
        $bag = new CBase_MessageBag(['nama' => ['wajib diisi']]);

        $this->assertFalse($bag->has('email'));
        $this->assertSame([], $bag->get('email'));
    }

    public function testHasWithSeveralKeysDemandsAllOfThem() {
        $bag = new CBase_MessageBag(['nama' => ['a'], 'email' => ['b']]);

        $this->assertTrue($bag->has(['nama', 'email']));
        $this->assertFalse($bag->has(['nama', 'umur']));
    }

    public function testHasAnyIsSatisfiedByOne() {
        $bag = new CBase_MessageBag(['nama' => ['a']]);

        $this->assertTrue($bag->hasAny(['nama', 'umur']));
        $this->assertFalse($bag->hasAny(['umur', 'alamat']));
    }

    public function testFirstReturnsTheFirstMessageOfAKey() {
        $bag = new CBase_MessageBag(['nama' => ['satu', 'dua']]);

        $this->assertSame('satu', $bag->first('nama'));
    }

    public function testFirstWithoutAKeyReturnsTheFirstMessageInTheBag() {
        $bag = new CBase_MessageBag(['nama' => ['satu'], 'email' => ['dua']]);

        $this->assertSame('satu', $bag->first());
    }

    public function testFirstIsAnEmptyStringWhenThereIsNothing() {
        $this->assertSame('', (new CBase_MessageBag())->first());
        $this->assertSame('', (new CBase_MessageBag())->first('nama'));
    }

    public function testAllFlattensEveryKey() {
        $bag = new CBase_MessageBag(['nama' => ['a', 'b'], 'email' => ['c']]);

        $this->assertSame(['a', 'b', 'c'], $bag->all());
    }

    public function testUniqueDropsRepeatedMessagesAcrossKeys() {
        $bag = new CBase_MessageBag(['nama' => ['wajib diisi'], 'email' => ['wajib diisi']]);

        $this->assertCount(2, $bag->all());
        $this->assertCount(1, $bag->unique());
    }

    /**
     * Format menentukan bagaimana tiap pesan dibungkus saat dibaca, sehingga
     * tampilan dapat menyisipkan penanda tanpa mengubah pesannya.
     */
    public function testAFormatWrapsEveryMessage() {
        $bag = new CBase_MessageBag(['nama' => ['wajib diisi']]);

        $this->assertSame(['<li>wajib diisi</li>'], $bag->get('nama', '<li>:message</li>'));
        $this->assertSame(['<li>wajib diisi</li>'], $bag->all('<li>:message</li>'));
        $this->assertSame('<li>wajib diisi</li>', $bag->first('nama', '<li>:message</li>'));
    }

    public function testTheDefaultFormatCanBeReplacedForTheWholeBag() {
        $bag = new CBase_MessageBag(['nama' => ['wajib diisi']]);

        $this->assertSame(':message', $bag->getFormat());

        $bag->setFormat('<p>:message</p>');

        $this->assertSame('<p>:message</p>', $bag->getFormat());
        $this->assertSame(['<p>wajib diisi</p>'], $bag->all());
    }

    /**
     * Kunci berjoker mengumpulkan pesan dari cabang yang cocok -- itulah cara
     * membaca galat sebuah larik masukan tanpa tahu berapa barisnya.
     */
    public function testAWildcardKeyCollectsTheMatchingBranches() {
        $bag = new CBase_MessageBag([
            'barang.0.nama' => ['wajib diisi'],
            'barang.1.nama' => ['terlalu panjang'],
            'pembeli.nama' => ['wajib diisi'],
        ]);

        $hasil = $bag->get('barang.*.nama');

        $this->assertCount(2, $hasil);
        $this->assertSame(['wajib diisi'], $hasil['barang.0.nama']);
        $this->assertSame(['terlalu panjang'], $hasil['barang.1.nama']);
    }

    public function testMergeBringsInAnotherSetOfMessages() {
        $bag = new CBase_MessageBag(['nama' => ['a']]);
        $bag->merge(['email' => ['b']]);

        $this->assertTrue($bag->has('nama'));
        $this->assertTrue($bag->has('email'));
    }

    public function testMergeAcceptsAnotherBag() {
        $bag = new CBase_MessageBag(['nama' => ['a']]);
        $bag->merge(new CBase_MessageBag(['email' => ['b']]));

        $this->assertSame(['a', 'b'], $bag->all());
    }

    public function testKeysListsEveryFieldThatHasAMessage() {
        $bag = new CBase_MessageBag(['nama' => ['a'], 'email' => ['b']]);

        $this->assertSame(['nama', 'email'], $bag->keys());
    }

    public function testTheBagCanBeHandedAroundAsItsOwnProvider() {
        $bag = new CBase_MessageBag(['nama' => ['a']]);

        $this->assertSame($bag, $bag->getMessageBag());
        $this->assertSame(['nama' => ['a']], $bag->messages());
        $this->assertSame(['nama' => ['a']], $bag->getMessages());
        $this->assertSame(['nama' => ['a']], $bag->toArray());
    }

    /**
     * Nilai berupa string tunggal dinormalkan menjadi larik saat masuk, jadi
     * pemanggil tidak perlu selalu membungkusnya sendiri.
     */
    public function testASinglePlainStringIsNormalisedIntoAnArray() {
        $bag = new CBase_MessageBag(['nama' => 'wajib diisi']);

        $this->assertSame(['wajib diisi'], $bag->get('nama'));
    }

    public function testHasWithoutAKeyAsksWhetherThereIsAnythingAtAll() {
        $this->assertFalse((new CBase_MessageBag())->has(null));
        $this->assertTrue((new CBase_MessageBag(['nama' => ['a']]))->has(null));
    }

    public function testHasAnyWithNoKeysIsFalse() {
        $this->assertFalse((new CBase_MessageBag(['nama' => ['a']]))->hasAny([]));
    }

    public function testFirstAlsoUnderstandsAWildcardKey() {
        $bag = new CBase_MessageBag(['barang.0.nama' => ['wajib diisi']]);

        $this->assertSame('wajib diisi', $bag->first('barang.*.nama'));
    }

    /**
     * Selain :message, format mengenal :key -- itulah cara menandai galat
     * dengan nama medannya tanpa menyusun pesannya sendiri.
     */
    public function testTheKeyPlaceholderIsReplacedToo() {
        $bag = new CBase_MessageBag(['nama' => ['wajib diisi']]);

        $this->assertSame(['nama: wajib diisi'], $bag->all(':key: :message'));
    }

    public function testTheBagCountsAsACountable() {
        $bag = new CBase_MessageBag(['nama' => ['a', 'b'], 'email' => ['c']]);

        $this->assertCount(3, $bag);
    }

    public function testTheBagSerializesToJson() {
        $bag = new CBase_MessageBag(['nama' => ['a']]);

        $this->assertSame('{"nama":["a"]}', $bag->toJson());
        $this->assertSame('{"nama":["a"]}', (string) $bag);
        $this->assertSame(['nama' => ['a']], $bag->jsonSerialize());
    }

    public function testJsonFlagsArePassedThrough() {
        $bag = new CBase_MessageBag(['nama' => ['a']]);

        $this->assertSame("{\n    \"nama\": [\n        \"a\"\n    ]\n}", $bag->toJson(JSON_PRETTY_PRINT));
    }
}
