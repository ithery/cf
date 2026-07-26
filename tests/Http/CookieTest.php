<?php

use PHPUnit\Framework\TestCase;

/**
 * Tests for CHTTP_Cookie (system/libraries/CHTTP/Cookie.php), the cookie-jar style
 * factory that builds and queues Symfony\Component\HttpFoundation\Cookie instances.
 */
class CookieTest extends TestCase {
    /**
     * @var CHTTP_Cookie
     */
    protected $jar;

    protected function setUp(): void {
        parent::setUp();

        $this->jar = new CHTTP_Cookie();
    }

    public function testMakeReturnsSymfonyCookieWithDefaults() {
        $cookie = $this->jar->make('name', 'value');

        $this->assertInstanceOf(Symfony\Component\HttpFoundation\Cookie::class, $cookie);
        $this->assertSame('name', $cookie->getName());
        $this->assertSame('value', $cookie->getValue());
        $this->assertSame('/', $cookie->getPath());
        $this->assertNull($cookie->getDomain());
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertSame('lax', $cookie->getSameSite());
        // minutes = 0 (default) means a session cookie: no expiry timestamp.
        $this->assertSame(0, $cookie->getExpiresTime());
    }

    public function testMakeSetsExpiresTimeFromMinutes() {
        $before = time();
        $cookie = $this->jar->make('name', 'value', 5);
        $after = time();

        $this->assertGreaterThanOrEqual($before + 5 * 60, $cookie->getExpiresTime());
        $this->assertLessThanOrEqual($after + 5 * 60, $cookie->getExpiresTime());
    }

    public function testMakeHonorsExplicitPathDomainSecureHttpOnlySameSite() {
        $cookie = $this->jar->make(
            'name',
            'value',
            0,
            '/custom',
            'example.com',
            true,
            false,
            false,
            'strict'
        );

        $this->assertSame('/custom', $cookie->getPath());
        $this->assertSame('example.com', $cookie->getDomain());
        $this->assertTrue($cookie->isSecure());
        $this->assertFalse($cookie->isHttpOnly());
        $this->assertSame('strict', $cookie->getSameSite());
    }

    public function testForeverCreatesLongLivedCookie() {
        $before = time();
        $cookie = $this->jar->forever('name', 'value');

        // ~5 years = 2628000 minutes.
        $this->assertGreaterThan($before + 2628000 * 60 - 5, $cookie->getExpiresTime());
    }

    public function testForgetCreatesExpiredCookieWithNullValue() {
        $cookie = $this->jar->forget('name');

        $this->assertNull($cookie->getValue());
        $this->assertLessThan(time(), $cookie->getExpiresTime());
        $this->assertTrue($cookie->isCleared());
    }

    public function testSetDefaultPathAndDomainAffectsSubsequentCookies() {
        $result = $this->jar->setDefaultPathAndDomain('/app', 'test.local', true, 'strict');
        $this->assertSame($this->jar, $result);

        $cookie = $this->jar->make('name', 'value');

        $this->assertSame('/app', $cookie->getPath());
        $this->assertSame('test.local', $cookie->getDomain());
        $this->assertTrue($cookie->isSecure());
        $this->assertSame('strict', $cookie->getSameSite());
    }

    public function testQueueWithParametersBuildsAndStoresCookie() {
        $this->jar->queue('name', 'value');

        $this->assertTrue($this->jar->hasQueued('name'));
        $queued = $this->jar->queued('name');
        $this->assertInstanceOf(Symfony\Component\HttpFoundation\Cookie::class, $queued);
        $this->assertSame('value', $queued->getValue());
    }

    public function testQueueWithCookieInstance() {
        $cookie = new Symfony\Component\HttpFoundation\Cookie('direct', 'val');
        $this->jar->queue($cookie);

        $this->assertTrue($this->jar->hasQueued('direct'));
        $this->assertSame($cookie, $this->jar->queued('direct'));
    }

    public function testQueuedReturnsNullDefaultWhenNotQueued() {
        // With no $default argument, carr::get() falls back to null, and
        // carr::last(null, ...) short-circuits via the empty() branch, so this path works.
        $this->assertNull($this->jar->queued('missing'));
    }

    public function testQueuedWithNonArrayDefaultReturnsTheDefault() {
        $this->assertSame('fallback', $this->jar->queued('missing', 'fallback'));
    }

    public function testHasQueuedIsFalseWhenNotQueued() {
        $this->assertFalse($this->jar->hasQueued('missing'));
    }

    public function testUnqueueRemovesCookieByName() {
        $this->jar->queue('name', 'value');
        $this->assertTrue($this->jar->hasQueued('name'));

        $this->jar->unqueue('name');

        $this->assertFalse($this->jar->hasQueued('name'));
    }

    public function testUnqueueRemovesCookieByNameAndPath() {
        $this->jar->queue('name', 'value1', 0, '/one');
        $this->jar->queue('name', 'value2', 0, '/two');

        $this->jar->unqueue('name', '/one');

        $this->assertTrue($this->jar->hasQueued('name', '/two'));
        $this->assertFalse($this->jar->hasQueued('name', '/one'));
    }

    public function testGetQueuedCookiesReturnsFlattenedList() {
        $this->jar->queue('a', '1');
        $this->jar->queue('b', '2');

        $queued = $this->jar->getQueuedCookies();

        $this->assertCount(2, $queued);
        foreach ($queued as $cookie) {
            $this->assertInstanceOf(Symfony\Component\HttpFoundation\Cookie::class, $cookie);
        }
    }

    public function testSymfonyCookieToStringContainsExpectedAttributes() {
        $cookie = $this->jar->make('token', 'abc123', 5, '/', 'example.com', true, true, false, 'strict');

        $header = (string) $cookie;

        $this->assertStringContainsString('token=abc123', $header);
        $this->assertStringContainsString('path=/', $header);
        $this->assertStringContainsString('domain=example.com', $header);
        $this->assertStringContainsString('secure', $header);
        $this->assertStringContainsString('httponly', $header);
        $this->assertStringContainsString('samesite=strict', $header);
    }

    public function testSymfonyCookieToStringForClearedCookie() {
        $cookie = $this->jar->forget('name');

        $this->assertStringContainsString('name=deleted', (string) $cookie);
    }
}
