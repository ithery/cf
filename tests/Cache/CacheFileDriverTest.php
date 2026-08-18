<?php
use PHPUnit\Framework\TestCase;

class CacheFileDriverTest extends TestCase {
    protected function makeStore() {
        return new CCache_Driver_FileDriver(['options' => ['directory' => 'test-file-driver']]);
    }

    protected function tearDown(): void {
        $this->makeStore()->flush();
        parent::tearDown();
    }

    public function testItemsCanBeSetAndRetrieved() {
        $store = $this->makeStore();
        $result = $store->put('foo', 'bar', 10);
        $this->assertTrue($result);
        $this->assertSame('bar', $store->get('foo'));
    }

    public function testItemsCanBeFlushed() {
        $store = $this->makeStore();
        $store->put('foo', 'bar', 10);
        $result = $store->flush();
        $this->assertTrue($result);
        $this->assertNull($store->get('foo'));
    }

    /**
     * flush() deletes the whole directory a CTemporary_File was created against. The engine's
     * CTemporary_File instances stayed alive and kept pointing at that now-gone directory,
     * because CTemporary_File::getPath() built the path by string concatenation instead of
     * going through CTemporary_Directory::getPath($filename) - the one that recreates a missing
     * directory. So put() after flush() used to fail with "No such file or directory".
     */
    public function testCanPutAgainAfterFlush() {
        $store = $this->makeStore();
        $store->put('foo', 'bar', 10);

        $store->flush();

        $result = $store->put('foo', 'baz', 10);
        $this->assertTrue($result);
        $this->assertSame('baz', $store->get('foo'));
    }
}
