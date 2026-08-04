<?php

use PHPUnit\Framework\TestCase;

/**
 * Keputusan mode maintenance.
 *
 * Yang diuji `CF::maintenanceDecision()` — bagian murni dari
 * `CF::isDownForMaintenance()`. Pemisahannya disengaja: keputusannya bergantung
 * pada konfigurasi, jalur, dan cookie saja, sehingga seluruh cabangnya dapat
 * diperiksa tanpa server, berkas, maupun request.
 */
class Core_MaintenanceTest extends TestCase {
    const SECRET = 'a7f3c9e21b4d5680';

    /**
     * @return array
     */
    protected function config(array $override = []) {
        return array_merge([
            'down' => true,
            'view' => 'system.maintenance',
            'secret' => self::SECRET,
            'cookie' => '',
        ], $override);
    }

    // ------------------------------------------------------------------
    // aplikasi hidup
    // ------------------------------------------------------------------

    public function testApplicationIsUpWhenDownIsFalse() {
        $this->assertSame(
            CF::MAINTENANCE_UP,
            CF::maintenanceDecision($this->config(['down' => false]), '/')
        );
    }

    /**
     * Kunci `down` yang hilang berarti **hidup**.
     *
     * Sebelumnya bawaannya `true`, sehingga berkas yang tidak lengkap atau
     * salah ketik menjatuhkan aplikasi. Kesalahan menulis konfigurasi tidak
     * seharusnya berbiaya downtime, dan test ini yang menjaga arah itu.
     */
    public function testApplicationIsUpWhenDownKeyIsMissing() {
        $this->assertSame(
            CF::MAINTENANCE_UP,
            CF::maintenanceDecision(['view' => 'system.maintenance'], '/')
        );
    }

    public function testApplicationIsUpWhenConfigIsNotAnArray() {
        $this->assertSame(CF::MAINTENANCE_UP, CF::maintenanceDecision(null, '/'));
        $this->assertSame(CF::MAINTENANCE_UP, CF::maintenanceDecision('down', '/'));
        $this->assertSame(CF::MAINTENANCE_UP, CF::maintenanceDecision(1, '/'));
    }

    // ------------------------------------------------------------------
    // aplikasi tutup
    // ------------------------------------------------------------------

    public function testApplicationIsDownForAnOrdinaryVisitor() {
        $this->assertSame(CF::MAINTENANCE_DOWN, CF::maintenanceDecision($this->config(), '/'));
        $this->assertSame(CF::MAINTENANCE_DOWN, CF::maintenanceDecision($this->config(), 'product/list'));
    }

    // ------------------------------------------------------------------
    // tautan rahasia
    // ------------------------------------------------------------------

    public function testOpeningTheSecretLinkGrantsAccess() {
        $this->assertSame(
            CF::MAINTENANCE_GRANT,
            CF::maintenanceDecision($this->config(), self::SECRET)
        );
    }

    public function testTheSecretLinkIsAcceptedWithSurroundingSlashes() {
        $this->assertSame(CF::MAINTENANCE_GRANT, CF::maintenanceDecision($this->config(), '/' . self::SECRET));
        $this->assertSame(CF::MAINTENANCE_GRANT, CF::maintenanceDecision($this->config(), self::SECRET . '/'));
    }

    /**
     * Jalur yang sekadar diawali rahasianya tidak cukup — bila cocok sebagian
     * saja diterima, seluruh sub-jalur di bawahnya ikut membuka pintu.
     */
    public function testAPathThatMerelyStartsWithTheSecretIsNotEnough() {
        $this->assertSame(
            CF::MAINTENANCE_DOWN,
            CF::maintenanceDecision($this->config(), self::SECRET . '/product')
        );
    }

    public function testAnEmptySecretDoesNotTurnEveryRequestIntoTheSecretLink() {
        $config = $this->config(['secret' => '']);

        $this->assertSame(CF::MAINTENANCE_DOWN, CF::maintenanceDecision($config, ''));
        $this->assertSame(CF::MAINTENANCE_DOWN, CF::maintenanceDecision($config, '/'));
    }

    // ------------------------------------------------------------------
    // cookie hasil tautan rahasia
    // ------------------------------------------------------------------

    public function testTheGrantedCookieBypassesMaintenance() {
        $this->assertSame(
            CF::MAINTENANCE_BYPASS,
            CF::maintenanceDecision($this->config(), 'product/list', [CF::MAINTENANCE_COOKIE => self::SECRET])
        );
    }

    /**
     * Nilainya yang diperiksa, bukan keberadaannya — inilah bedanya dengan
     * bentuk lama, dan alasan bentuk ini yang dianjurkan.
     */
    public function testACookieWithTheWrongValueDoesNotBypass() {
        $config = $this->config();

        $this->assertSame(
            CF::MAINTENANCE_DOWN,
            CF::maintenanceDecision($config, '/', [CF::MAINTENANCE_COOKIE => 'tebakan'])
        );
        $this->assertSame(
            CF::MAINTENANCE_DOWN,
            CF::maintenanceDecision($config, '/', [CF::MAINTENANCE_COOKIE => ''])
        );
        $this->assertSame(
            CF::MAINTENANCE_DOWN,
            CF::maintenanceDecision($config, '/', [CF::MAINTENANCE_COOKIE => substr(self::SECRET, 0, 8)])
        );
    }

    // ------------------------------------------------------------------
    // bentuk lama
    // ------------------------------------------------------------------

    /**
     * Dipertahankan supaya konfigurasi yang sudah terpasang tidak patah.
     * Kelemahannya melekat: yang diperiksa hanya keberadaan cookie, sehingga
     * **nama** cookie itulah rahasianya.
     */
    public function testTheLegacyCookieNameStillBypasses() {
        $config = $this->config(['secret' => '', 'cookie' => 'bypass-maintenance']);

        $this->assertSame(
            CF::MAINTENANCE_BYPASS,
            CF::maintenanceDecision($config, '/', ['bypass-maintenance' => '1'])
        );
    }

    public function testTheLegacyCookieBypassesRegardlessOfItsValue() {
        $config = $this->config(['secret' => '', 'cookie' => 'bypass-maintenance']);

        $this->assertSame(
            CF::MAINTENANCE_BYPASS,
            CF::maintenanceDecision($config, '/', ['bypass-maintenance' => ''])
        );
    }

    public function testAnEmptyLegacyCookieNameNeverBypasses() {
        $config = $this->config(['secret' => '', 'cookie' => '']);

        $this->assertSame(CF::MAINTENANCE_DOWN, CF::maintenanceDecision($config, '/', ['' => '1']));
        $this->assertSame(CF::MAINTENANCE_DOWN, CF::maintenanceDecision($config, '/', []));
    }

    public function testBothMechanismsMayBeConfiguredTogether() {
        $config = $this->config(['cookie' => 'bypass-maintenance']);

        $this->assertSame(
            CF::MAINTENANCE_BYPASS,
            CF::maintenanceDecision($config, '/', [CF::MAINTENANCE_COOKIE => self::SECRET])
        );
        $this->assertSame(
            CF::MAINTENANCE_BYPASS,
            CF::maintenanceDecision($config, '/', ['bypass-maintenance' => '1'])
        );
        $this->assertSame(CF::MAINTENANCE_DOWN, CF::maintenanceDecision($config, '/'));
    }

    // ------------------------------------------------------------------
    // penjagaan yang berlaku saat aplikasi hidup
    // ------------------------------------------------------------------

    /**
     * Saat aplikasi hidup, tautan rahasianya tidak boleh membajak jalur apa
     * pun — bila ia tetap diperiksa, sebuah rute yang kebetulan bernama sama
     * dengan rahasianya akan dialihkan alih-alih dilayani.
     */
    public function testTheSecretLinkIsInertWhileTheApplicationIsUp() {
        $this->assertSame(
            CF::MAINTENANCE_UP,
            CF::maintenanceDecision($this->config(['down' => false]), self::SECRET)
        );
    }
}
