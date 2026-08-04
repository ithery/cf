<?php

use PHPUnit\Framework\TestCase;

class QueueExceptionTestJob extends CQueue_AbstractJob {
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
        return json_encode(['displayName' => 'PekerjaanUji', 'job' => 'PekerjaanUji@handle', 'data' => []]);
    }

    /**
     * @return int
     */
    public function attempts() {
        return 1;
    }
}

class QueueExceptionTest extends TestCase {
    public function testMaxAttemptsExceededNamesTheJob() {
        $exception = CQueue_Exception_MaxAttemptsExceededException::forJob(new QueueExceptionTestJob());

        $this->assertInstanceOf(CQueue_Exception_MaxAttemptsExceededException::class, $exception);
        $this->assertTrue(strpos($exception->getMessage(), 'PekerjaanUji') !== false, $exception->getMessage());
    }

    public function testMaxAttemptsExceededKeepsTheJob() {
        $job = new QueueExceptionTestJob();

        $this->assertSame($job, CQueue_Exception_MaxAttemptsExceededException::forJob($job)->job);
    }

    public function testTimeoutExceededNamesTheJob() {
        $exception = CQueue_Exception_TimeoutExceededException::forJob(new QueueExceptionTestJob());

        $this->assertTrue(strpos($exception->getMessage(), 'PekerjaanUji') !== false, $exception->getMessage());
        $this->assertTrue(strpos($exception->getMessage(), 'timed out') !== false, $exception->getMessage());
    }

    public function testTimeoutExceededKeepsTheJob() {
        $job = new QueueExceptionTestJob();

        $this->assertSame($job, CQueue_Exception_TimeoutExceededException::forJob($job)->job);
    }

    /**
     * A timeout is a kind of "attempted too many times", so code already
     * catching the latter keeps catching the former.
     */
    public function testTimeoutExceededIsAMaxAttemptsExceeded() {
        $exception = CQueue_Exception_TimeoutExceededException::forJob(new QueueExceptionTestJob());

        $this->assertInstanceOf(CQueue_Exception_MaxAttemptsExceededException::class, $exception);
    }

    public function testTheTwoMessagesAreDistinguishable() {
        $job = new QueueExceptionTestJob();
        $timeout = CQueue_Exception_TimeoutExceededException::forJob($job);
        $attempts = CQueue_Exception_MaxAttemptsExceededException::forJob($job);

        $this->assertNotSame($timeout->getMessage(), $attempts->getMessage());
    }

    public function testTheyRemainPlainExceptions() {
        $this->assertInstanceOf(
            Exception::class,
            CQueue_Exception_MaxAttemptsExceededException::forJob(new QueueExceptionTestJob())
        );
    }
}
