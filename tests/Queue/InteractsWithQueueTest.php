<?php

use PHPUnit\Framework\TestCase;

class InteractsWithQueueTestJobRecorder extends CQueue_AbstractJob {
    /**
     * @var array
     */
    public $calls = [];

    /**
     * @var int
     */
    public $attemptCount = 3;

    /**
     * @return string
     */
    public function getJobId() {
        return 'job-1';
    }

    /**
     * @return string
     */
    public function getRawBody() {
        return json_encode(['job' => 'Uji@handle', 'data' => []]);
    }

    /**
     * @return int
     */
    public function attempts() {
        return $this->attemptCount;
    }

    /**
     * @param int $delay
     *
     * @return void
     */
    public function release($delay = 0) {
        $this->calls[] = ['release', $delay];
        parent::release($delay);
    }

    /**
     * @return void
     */
    public function delete() {
        $this->calls[] = ['delete', null];
        parent::delete();
    }

    /**
     * @param null|Throwable $e
     *
     * @return void
     */
    public function fail($e = null) {
        $this->calls[] = ['fail', $e];
        $this->failed = true;
    }
}

class InteractsWithQueueTestCommand {
    use CQueue_Trait_InteractsWithQueue;
}

class InteractsWithQueueTest extends TestCase {
    /**
     * Tanpa job terpasang, trait ini harus diam alih-alih fatal — kelas perintah
     * yang sama juga dijalankan sinkron, dan di sana tidak ada job sama sekali.
     * attempts() menjawab 1, bukan 0: eksekusi yang sedang berjalan itu sendiri
     * sudah terhitung percobaan pertama.
     */
    public function testEverythingIsANoOpWithoutAJob() {
        $command = new InteractsWithQueueTestCommand();

        $this->assertSame(1, $command->attempts());
        $this->assertNull($command->delete());
        $this->assertNull($command->release());
        $this->assertNull($command->fail());
    }

    public function testAttemptsComesFromTheJob() {
        $command = new InteractsWithQueueTestCommand();
        $job = new InteractsWithQueueTestJobRecorder();
        $job->attemptCount = 7;
        $command->setJob($job);

        $this->assertSame(7, $command->attempts());
    }

    public function testDeleteIsForwardedToTheJob() {
        $command = new InteractsWithQueueTestCommand();
        $job = new InteractsWithQueueTestJobRecorder();
        $command->setJob($job);
        $command->delete();

        $this->assertSame([['delete', null]], $job->calls);
        $this->assertTrue($job->isDeleted());
    }

    public function testReleaseIsForwardedWithItsDelay() {
        $command = new InteractsWithQueueTestCommand();
        $job = new InteractsWithQueueTestJobRecorder();
        $command->setJob($job);
        $command->release(30);

        $this->assertSame([['release', 30]], $job->calls);
        $this->assertTrue($job->isReleased());
    }

    public function testReleaseDefaultsToNoDelay() {
        $command = new InteractsWithQueueTestCommand();
        $job = new InteractsWithQueueTestJobRecorder();
        $command->setJob($job);
        $command->release();

        $this->assertSame([['release', 0]], $job->calls);
    }

    public function testFailIsForwardedWithItsException() {
        $command = new InteractsWithQueueTestCommand();
        $job = new InteractsWithQueueTestJobRecorder();
        $command->setJob($job);
        $boom = new RuntimeException('meledak');
        $command->fail($boom);

        $this->assertSame([['fail', $boom]], $job->calls);
        $this->assertTrue($job->hasFailed());
    }

    public function testSetJobReturnsTheCommandForChaining() {
        $command = new InteractsWithQueueTestCommand();

        $this->assertSame($command, $command->setJob(new InteractsWithQueueTestJobRecorder()));
    }

    public function testDeletedAndReleasedStartFalse() {
        $job = new InteractsWithQueueTestJobRecorder();

        $this->assertFalse($job->isDeleted());
        $this->assertFalse($job->isReleased());
        $this->assertFalse($job->isDeletedOrReleased());
    }

    public function testIsDeletedOrReleasedCoversBoth() {
        $job = new InteractsWithQueueTestJobRecorder();
        $job->release(0);

        $this->assertTrue($job->isDeletedOrReleased());
    }
}
