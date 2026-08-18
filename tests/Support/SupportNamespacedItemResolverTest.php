<?php

use PHPUnit\Framework\TestCase;

class SupportNamespacedItemResolverTestResolver extends CBase_NamespacedItemResolver {
}

class SupportNamespacedItemResolverTest extends TestCase {
    /**
     * @return CBase_NamespacedItemResolver
     */
    protected function makeResolver() {
        return new SupportNamespacedItemResolverTestResolver();
    }

    /**
     * Kunci diurai menjadi [namespace, berkas, item]. Itulah yang menentukan
     * berkas konfigurasi mana yang dibuka untuk `CF::config('app.name')`.
     */
    public function testAGroupAndAnItemAreSeparated() {
        $this->assertSame([null, 'app', 'name'], $this->makeResolver()->parseKey('app.name'));
    }

    public function testANestedItemKeepsItsRemainingDots() {
        $this->assertSame([null, 'app', 'providers.web'], $this->makeResolver()->parseKey('app.providers.web'));
    }

    /**
     * Satu ruas tanpa titik bukan berarti "seluruh berkas": di CF ia dibaca
     * sebagai item di dalam berkas `core`. Berbeda dari kebiasaan hulu, jadi
     * dikunci di sini supaya tidak diam-diam berubah.
     */
    public function testASingleSegmentFallsBackToTheCoreGroup() {
        $this->assertSame([null, 'core', 'app'], $this->makeResolver()->parseKey('app'));
    }

    public function testANamespaceIsSplitOffFirst() {
        $this->assertSame(['modul', 'app', 'name'], $this->makeResolver()->parseKey('modul::app.name'));
    }

    public function testANamespacedSingleSegmentAlsoFallsBackToCore() {
        $this->assertSame(['modul', 'core', 'app'], $this->makeResolver()->parseKey('modul::app'));
    }

    public function testANamespacedNestedItemKeepsItsRemainingDots() {
        $this->assertSame(
            ['modul', 'app', 'providers.web'],
            $this->makeResolver()->parseKey('modul::app.providers.web')
        );
    }

    /**
     * Hasil uraian disimpan, karena kunci yang sama dibaca berkali-kali dalam
     * satu permintaan.
     */
    public function testTheParsedKeyIsRemembered() {
        $resolver = $this->makeResolver();
        $pertama = $resolver->parseKey('app.name');
        $kedua = $resolver->parseKey('app.name');

        $this->assertSame($pertama, $kedua);
    }

    public function testAParsedKeyCanBeSetByHand() {
        $resolver = $this->makeResolver();
        $resolver->setParsedKey('app.name', ['sendiri', 'diatur', 'tangan']);

        $this->assertSame(['sendiri', 'diatur', 'tangan'], $resolver->parseKey('app.name'));
    }
}
