<?php

use PHPUnit\Framework\TestCase;

class QueueNullQueueTest extends TestCase {
    /**
     * @return CQueue_Queue_NullQueue
     */
    protected function makeQueue() {
        $queue = new CQueue_Queue_NullQueue();
        $queue->setContainer(CContainer::getInstance());
        $queue->setConnectionName('null');

        return $queue;
    }

    /**
     * The null driver exists so a job dispatch can be turned into a no-op
     * without touching the calling code. Everything must therefore succeed
     * quietly rather than throw.
     */
    public function testSizeIsAlwaysZero() {
        $this->assertSame(0, $this->makeQueue()->size());
        $this->assertSame(0, $this->makeQueue()->size('apa-pun'));
    }

    public function testPushSwallowsTheJob() {
        $this->assertNull($this->makeQueue()->push('PekerjaanUji', ['id' => 1]));
    }

    public function testPushRawSwallowsThePayload() {
        $this->assertNull($this->makeQueue()->pushRaw('mentah'));
    }

    public function testLaterSwallowsTheJob() {
        $this->assertNull($this->makeQueue()->later(60, 'PekerjaanUji'));
    }

    public function testPushOnSwallowsTheJob() {
        $this->assertNull($this->makeQueue()->pushOn('lainnya', 'PekerjaanUji'));
    }

    public function testLaterOnSwallowsTheJob() {
        $this->assertNull($this->makeQueue()->laterOn('lainnya', 60, 'PekerjaanUji'));
    }

    public function testPopNeverGivesAJob() {
        $this->assertNull($this->makeQueue()->pop());
        $this->assertNull($this->makeQueue()->pop('apa-pun'));
    }

    public function testBulkSwallowsEveryJob() {
        $this->assertNull($this->makeQueue()->bulk(['SatuJob', 'DuaJob'], ['id' => 1]));
    }

    public function testTheConnectionNameIsStillCarried() {
        $this->assertSame('null', $this->makeQueue()->getConnectionName());
    }
}
