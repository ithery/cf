<?php

use PHPUnit\Framework\TestCase;

/**
 * Ketergantungan driver antrean beanstalkd.
 *
 * Pheanstalk sebelumnya duduk di `modules/cresenity/vendor/`, sedangkan
 * `modules/` ditargetkan hilang seluruhnya pada CF 1.9. Begitu folder itu
 * dihapus, `CQueue_Queue_BeanstalkdQueue` ikut mati - type hint konstruktornya
 * tidak dapat dipenuhi dan konektornya melempar saat dipanggil.
 *
 * Kegagalannya baru muncul ketika seseorang benar-benar memakai driver ini, jauh
 * dari perubahan yang menyebabkannya. Test ini yang menjadikannya terlihat
 * seketika: pustakanya harus ada, dan harus ada **di `system/vendor/`**.
 */
class QueueBeanstalkdDependencyTest extends TestCase {
    /**
     * @return void
     */
    public function testThePheanstalkClassesResolve() {
        $this->assertTrue(class_exists('Pheanstalk\Pheanstalk'));
        $this->assertTrue(class_exists('Pheanstalk\Connection'));
        $this->assertTrue(class_exists('Pheanstalk\Job'));
        $this->assertTrue(interface_exists('Pheanstalk\Contract\PheanstalkInterface'));
    }

    /**
     * Konstanta yang dipakai konektor dan antreannya. Semuanya diwarisi dari
     * `PheanstalkInterface`, bukan dideklarasikan di kelasnya - jadi pencarian
     * kasar di berkas kelas tidak akan menemukannya, dan sempat menimbulkan
     * kesimpulan keliru bahwa driver ini rusak.
     *
     * @return void
     */
    public function testTheConstantsTheConnectorReliesOnExist() {
        $this->assertSame(1024, Pheanstalk\Pheanstalk::DEFAULT_PRIORITY);
        $this->assertSame(0, Pheanstalk\Pheanstalk::DEFAULT_DELAY);
        $this->assertSame(60, Pheanstalk\Pheanstalk::DEFAULT_TTR);
        $this->assertSame(11300, Pheanstalk\Pheanstalk::DEFAULT_PORT);
        $this->assertSame(2, Pheanstalk\Connection::DEFAULT_CONNECT_TIMEOUT);
    }

    /**
     * Inti test ini: lokasinya, bukan sekadar keberadaannya.
     *
     * @return void
     */
    public function testPheanstalkLivesUnderSystemVendor() {
        $reflection = new ReflectionClass('Pheanstalk\Pheanstalk');
        $path = str_replace('\\', '/', (string) $reflection->getFileName());

        $this->assertStringContainsString(
            'system/vendor/Pheanstalk/',
            $path,
            'Pheanstalk tidak lagi di system/vendor - driver beanstalkd akan mati begitu modules/ dihapus'
        );
        $this->assertStringNotContainsString('modules/', $path);
    }

    /**
     * Dan kelas CF yang memakainya ikut dapat dimuat.
     *
     * @return void
     */
    public function testTheBeanstalkdDriverClassesLoad() {
        $this->assertTrue(class_exists('CQueue_Connector_BeanstalkdConnector'));
        $this->assertTrue(class_exists('CQueue_Queue_BeanstalkdQueue'));
    }
}
