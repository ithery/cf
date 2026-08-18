<?php

use PHPUnit\Framework\TestCase;

class SyncQueueTestHandler {
    /**
     * @var array
     */
    public static $seen = [];

    /**
     * @param CQueue_JobInterface $job
     * @param array               $data
     *
     * @return void
     */
    public function fire($job, $data) {
        static::$seen[] = $data;
        $job->delete();
    }
}

class SyncQueueTestFailingHandler {
    /**
     * @param CQueue_JobInterface $job
     * @param array               $data
     *
     * @return void
     */
    public function fire($job, $data) {
        throw new RuntimeException('meledak');
    }
}

class QueueSyncQueueTest extends TestCase {
    protected function setUp() {
        SyncQueueTestHandler::$seen = [];
    }

    /**
     * @return CQueue_Queue_SyncQueue
     */
    protected function makeQueue() {
        $queue = new CQueue_Queue_SyncQueue();
        $queue->setContainer(CContainer::getInstance());
        $queue->setConnectionName('sync');

        return $queue;
    }

    /**
     * The whole point of the sync driver: the job runs inside push(), not later.
     */
    public function testPushRunsTheJobImmediately() {
        $queue = $this->makeQueue();
        $queue->push(SyncQueueTestHandler::class, ['id' => 1]);

        $this->assertSame([['id' => 1]], SyncQueueTestHandler::$seen);
    }

    public function testLaterAlsoRunsImmediatelyBecauseThereIsNothingToWaitFor() {
        $queue = $this->makeQueue();
        $queue->later(60, SyncQueueTestHandler::class, ['id' => 2]);

        $this->assertSame([['id' => 2]], SyncQueueTestHandler::$seen);
    }

    public function testPushOnRunsTheJobAsWell() {
        $queue = $this->makeQueue();
        $queue->pushOn('apa-pun', SyncQueueTestHandler::class, ['id' => 3]);

        $this->assertSame([['id' => 3]], SyncQueueTestHandler::$seen);
    }

    /**
     * Nothing is ever stored, so the queue is always empty and never pops.
     */
    public function testSizeIsAlwaysZero() {
        $queue = $this->makeQueue();
        $queue->push(SyncQueueTestHandler::class, ['id' => 4]);

        $this->assertSame(0, $queue->size());
    }

    public function testPopReturnsNothing() {
        $queue = $this->makeQueue();

        $this->assertNull($queue->pop());
    }

    /**
     * A failure is not swallowed: it reaches the caller, because with the sync
     * driver the caller *is* the worker.
     */
    public function testAnExceptionFromTheJobReachesTheCaller() {
        $queue = $this->makeQueue();

        $this->expectException(RuntimeException::class);
        $queue->push(SyncQueueTestFailingHandler::class, []);
    }

    public function testBulkRunsEveryJob() {
        $queue = $this->makeQueue();
        $queue->bulk([SyncQueueTestHandler::class, SyncQueueTestHandler::class], ['id' => 5]);

        $this->assertCount(2, SyncQueueTestHandler::$seen);
    }

    public function testTheConnectionNameIsCarried() {
        $queue = $this->makeQueue();

        $this->assertSame('sync', $queue->getConnectionName());
    }
}
