<?php

use PHPUnit\Framework\TestCase;

class CacheRateLimiterTest extends TestCase {
    protected function tearDown(): void {
        CCarbon::setTestNow(null);
        parent::tearDown();
    }

    /**
     * @return CCache_RateLimiter
     */
    protected function getRateLimiter() {
        return new CCache_RateLimiter(new CCache_Repository(new CCache_Driver_ArrayDriver()));
    }

    public function testHitIncrementsAttemptsAndReturnsCount() {
        $limiter = $this->getRateLimiter();

        $this->assertSame(1, $limiter->hit('key'));
        $this->assertSame(2, $limiter->hit('key'));
        $this->assertSame(3, $limiter->hit('key'));
    }

    public function testAttemptsReturnsZeroForUnknownKey() {
        $limiter = $this->getRateLimiter();
        $this->assertSame(0, $limiter->attempts('key'));
    }

    public function testAttemptsReturnsCurrentHitCount() {
        $limiter = $this->getRateLimiter();
        $limiter->hit('key');
        $limiter->hit('key');
        $this->assertSame(2, $limiter->attempts('key'));
    }

    public function testTooManyAttemptsReturnsFalseWhenUnderLimit() {
        $limiter = $this->getRateLimiter();
        $limiter->hit('key');
        $limiter->hit('key');
        $this->assertFalse($limiter->tooManyAttempts('key', 3));
    }

    public function testTooManyAttemptsReturnsTrueWhenAtLimit() {
        $limiter = $this->getRateLimiter();
        $limiter->hit('key');
        $limiter->hit('key');
        $limiter->hit('key');
        $this->assertTrue($limiter->tooManyAttempts('key', 3));
    }

    public function testTooManyAttemptsResetsAttemptsWhenTimerExpired() {
        CCarbon::setTestNow(CCarbon::now());
        $limiter = $this->getRateLimiter();

        $limiter->hit('key', 1);
        $limiter->hit('key', 1);
        $this->assertTrue($limiter->tooManyAttempts('key', 2));

        // Advance past the decay window so the ":timer" key expires; the
        // ArrayDriver drops "key" too since it was stored with the same TTL.
        CCarbon::setTestNow(CCarbon::now()->addSeconds(2));

        $this->assertFalse($limiter->tooManyAttempts('key', 2));
        $this->assertSame(0, $limiter->attempts('key'));
    }

    public function testResetAttemptsClearsTheCounter() {
        $limiter = $this->getRateLimiter();
        $limiter->hit('key');
        $limiter->hit('key');
        $limiter->resetAttempts('key');
        $this->assertSame(0, $limiter->attempts('key'));
    }

    public function testRetriesLeftReturnsRemainingAttempts() {
        $limiter = $this->getRateLimiter();
        $limiter->hit('key');
        $this->assertSame(4, $limiter->retriesLeft('key', 5));
    }

    public function testRetriesLeftWhenNoAttemptsYet() {
        $limiter = $this->getRateLimiter();
        $this->assertSame(5, $limiter->retriesLeft('key', 5));
    }

    public function testClearRemovesAttemptsAndTimer() {
        $limiter = $this->getRateLimiter();
        $limiter->hit('key', 60);
        $limiter->hit('key', 60);
        $limiter->hit('key', 60);

        $this->assertTrue($limiter->tooManyAttempts('key', 3));

        $limiter->clear('key');

        $this->assertSame(0, $limiter->attempts('key'));
        $this->assertFalse($limiter->tooManyAttempts('key', 3));
    }

    public function testAvailableInReturnsSecondsUntilTimerExpires() {
        CCarbon::setTestNow(CCarbon::now());
        $limiter = $this->getRateLimiter();
        $limiter->hit('key', 60);

        $availableIn = $limiter->availableIn('key');

        $this->assertGreaterThan(55, $availableIn);
        $this->assertLessThanOrEqual(60, $availableIn);
    }

    public function testAliasForRegistersANamedLimiterAndLimiterRetrievesIt() {
        $limiter = $this->getRateLimiter();
        $callback = function () {
            return 'the-limit';
        };

        $result = $limiter->aliasFor('login', $callback);

        $this->assertSame($limiter, $result);
        $this->assertSame($callback, $limiter->limiter('login'));
    }

    public function testLimiterReturnsNullForUnknownName() {
        $limiter = $this->getRateLimiter();
        $this->assertNull($limiter->limiter('unknown'));
    }

    public function testHitWithCustomDecaySecondsSetsTimer() {
        CCarbon::setTestNow(CCarbon::now());
        $limiter = $this->getRateLimiter();

        $limiter->hit('key', 120);

        $availableIn = $limiter->availableIn('key');
        $this->assertGreaterThan(115, $availableIn);
        $this->assertLessThanOrEqual(120, $availableIn);
    }

    public function testSeparateKeysAreTrackedIndependently() {
        $limiter = $this->getRateLimiter();
        $limiter->hit('key-a');
        $limiter->hit('key-a');
        $limiter->hit('key-b');

        $this->assertSame(2, $limiter->attempts('key-a'));
        $this->assertSame(1, $limiter->attempts('key-b'));
    }
}
