<?php

use PHPUnit\Framework\TestCase;

class QueuePauseResumeTest extends TestCase {
    protected function tearDown() {
        $manager = CQueue::queuer();
        foreach (['default', 'lambat', 'satu', 'dua'] as $queue) {
            $manager->resume('uji', $queue);
        }
    }

    /**
     * @return CQueue_Manager
     */
    protected function manager() {
        return CQueue::queuer();
    }

    public function testAQueueStartsUnpaused() {
        $this->assertFalse($this->manager()->isPaused('uji', 'default'));
    }

    public function testPauseThenResume() {
        $manager = $this->manager();
        $manager->pause('uji', 'default');
        $this->assertTrue($manager->isPaused('uji', 'default'));

        $manager->resume('uji', 'default');
        $this->assertFalse($manager->isPaused('uji', 'default'));
    }

    /**
     * Pausing is per queue, not per connection — otherwise pausing one slow
     * queue would silently stop everything on the same connection.
     */
    public function testPausingOneQueueLeavesTheOthersAlone() {
        $manager = $this->manager();
        $manager->pause('uji', 'lambat');

        $this->assertTrue($manager->isPaused('uji', 'lambat'));
        $this->assertFalse($manager->isPaused('uji', 'default'));
    }

    public function testPausingIsPerConnectionToo() {
        $manager = $this->manager();
        $manager->pause('uji', 'default');

        $this->assertTrue($manager->isPaused('uji', 'default'));
        $this->assertFalse($manager->isPaused('lain', 'default'));
    }

    public function testGetPausedQueuesListsOnlyThePausedOnes() {
        $manager = $this->manager();
        $manager->pause('uji', 'satu');

        $paused = $manager->getPausedQueues('uji', ['satu', 'dua']);

        $this->assertSame(['satu'], $paused);
    }

    public function testGetPausedQueuesIsEmptyWhenNothingIsPaused() {
        $manager = $this->manager();

        $this->assertSame([], $manager->getPausedQueues('uji', ['satu', 'dua']));
    }

    public function testResumingSomethingNeverPausedIsHarmless() {
        $manager = $this->manager();
        $manager->resume('uji', 'belum-pernah');

        $this->assertFalse($manager->isPaused('uji', 'belum-pernah'));
    }

    public function testPauseRaisesItsEvent() {
        $seen = null;
        CEvent::dispatcher()->listen(CQueue_Event_QueuePaused::class, function ($e) use (&$seen) {
            $seen = $e;
        });
        $this->manager()->pause('uji', 'default');

        $this->assertInstanceOf(CQueue_Event_QueuePaused::class, $seen);
        $this->assertSame('uji', $seen->connectionName);
        $this->assertSame('default', $seen->queue);
    }

    public function testResumeRaisesItsEvent() {
        $seen = null;
        CEvent::dispatcher()->listen(CQueue_Event_QueueResumed::class, function ($e) use (&$seen) {
            $seen = $e;
        });
        $this->manager()->resume('uji', 'default');

        $this->assertInstanceOf(CQueue_Event_QueueResumed::class, $seen);
        $this->assertSame('uji', $seen->connectionName);
        $this->assertSame('default', $seen->queue);
    }

    public function testPauseForCarriesItsTtlOnTheEvent() {
        $seen = null;
        CEvent::dispatcher()->listen(CQueue_Event_QueuePaused::class, function ($e) use (&$seen) {
            $seen = $e;
        });
        $this->manager()->pauseFor('uji', 'default', 60);

        $this->assertTrue($this->manager()->isPaused('uji', 'default'));
        $this->assertSame(60, $seen->ttl);
    }

    /**
     * The worker skips a paused queue rather than popping from it. Without a
     * cache it must not pay a lookup at all, which is why an unconfigured
     * worker reports nothing paused.
     */
    public function testAWorkerWithoutACacheSkipsThePausedLookupEntirely() {
        $manager = $this->manager();
        $manager->pause('uji', 'default');

        $handler = Mockery::mock(CException_ExceptionHandler::class);
        $handler->shouldIgnoreMissing();
        $worker = new CQueue_Worker($manager, new CEvent_Dispatcher(), $handler, function () {
            return false;
        });

        $method = new ReflectionMethod(CQueue_Worker::class, 'getPausedQueues');
        $method->setAccessible(true);

        $this->assertSame([], $method->invoke($worker, 'uji', ['default']));
        Mockery::close();
    }
}
