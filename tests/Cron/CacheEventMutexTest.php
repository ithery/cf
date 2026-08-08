<?php
use PHPUnit\Framework\TestCase;

class CacheEventMutexTest extends TestCase {
    /**
     * exists()/forget() referenced an undefined $this->cache property - only create()
     * correctly used CCache::manager(). Any ->withoutOverlapping() job crashed with
     * "Undefined property: $cache" the moment its overlap check ran.
     */
    public function testCreateExistsAndForgetRoundTrip() {
        $mutex = (new CCron_CacheEventMutex())->useStore('array');
        $event = new CCron_Event($mutex, 'php foo-' . __METHOD__);

        $this->assertFalse($mutex->exists($event));

        $this->assertTrue($mutex->create($event));
        $this->assertTrue($mutex->exists($event));

        $mutex->forget($event);
        $this->assertFalse($mutex->exists($event));
    }
}
