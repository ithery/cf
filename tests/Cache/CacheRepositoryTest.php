<?php

use PHPUnit\Framework\TestCase;

class CacheRepositoryTest extends TestCase {
    /**
     * @return CCache_Repository
     */
    protected function getRepository() {
        return new CCache_Repository(new CCache_Driver_ArrayDriver());
    }

    public function testGetReturnsValueFromDriver() {
        $repo = $this->getRepository();
        $repo->getDriver()->put('foo', 'bar', 10);
        $this->assertSame('bar', $repo->get('foo'));
    }

    public function testGetReturnsDefaultWhenNotFound() {
        $repo = $this->getRepository();
        $this->assertSame('default', $repo->get('foo', 'default'));
    }

    public function testGetReturnsNullWhenNotFoundAndNoDefault() {
        $repo = $this->getRepository();
        $this->assertNull($repo->get('foo'));
    }

    public function testGetDefaultCanBeAClosure() {
        $repo = $this->getRepository();
        $this->assertSame('default', $repo->get('foo', function () {
            return 'default';
        }));
    }

    public function testPutStoresValueForGivenSeconds() {
        $repo = $this->getRepository();
        $result = $repo->put('foo', 'bar', 10);
        $this->assertTrue($result);
        $this->assertSame('bar', $repo->get('foo'));
    }

    public function testSetIsAliasForPut() {
        $repo = $this->getRepository();
        $repo->set('foo', 'bar', 10);
        $this->assertSame('bar', $repo->get('foo'));
    }

    public function testPutWithNullTtlStoresForever() {
        $repo = $this->getRepository();
        $repo->put('foo', 'bar');
        $this->assertSame('bar', $repo->get('foo'));
    }

    public function testPutWithZeroOrNegativeSecondsForgetsKey() {
        $repo = $this->getRepository();
        $repo->put('foo', 'bar', 10);
        $repo->put('foo', 'baz', 0);
        $this->assertNull($repo->get('foo'));
    }

    public function testPutWithArrayKeyDelegatesToPutMany() {
        $repo = $this->getRepository();
        $repo->put(['foo' => 'bar', 'baz' => 'qux'], null, 10);
        $this->assertSame('bar', $repo->get('foo'));
        $this->assertSame('qux', $repo->get('baz'));
    }

    public function testHasReturnsTrueWhenPresent() {
        $repo = $this->getRepository();
        $repo->put('foo', 'bar', 10);
        $this->assertTrue($repo->has('foo'));
    }

    public function testHasReturnsFalseWhenMissing() {
        $repo = $this->getRepository();
        $this->assertFalse($repo->has('foo'));
    }

    public function testMissingReturnsOppositeOfHas() {
        $repo = $this->getRepository();
        $this->assertTrue($repo->missing('foo'));
        $repo->put('foo', 'bar', 10);
        $this->assertFalse($repo->missing('foo'));
    }

    public function testForeverStoresValueIndefinitely() {
        CCarbon::setTestNow(CCarbon::now());
        $repo = $this->getRepository();
        $repo->forever('foo', 'bar');
        CCarbon::setTestNow(CCarbon::now()->addYears(100));
        $this->assertSame('bar', $repo->get('foo'));
        CCarbon::setTestNow(null);
    }

    public function testForgetRemovesItem() {
        $repo = $this->getRepository();
        $repo->put('foo', 'bar', 10);
        $this->assertTrue($repo->forget('foo'));
        $this->assertNull($repo->get('foo'));
    }

    public function testForgetReturnsFalseWhenKeyDoesNotExist() {
        $repo = $this->getRepository();
        $this->assertFalse($repo->forget('foo'));
    }

    public function testDeleteIsAliasForForget() {
        $repo = $this->getRepository();
        $repo->put('foo', 'bar', 10);
        $this->assertTrue($repo->delete('foo'));
        $this->assertNull($repo->get('foo'));
    }

    public function testPullReturnsValueAndRemovesIt() {
        $repo = $this->getRepository();
        $repo->put('foo', 'bar', 10);
        $this->assertSame('bar', $repo->pull('foo'));
        $this->assertNull($repo->get('foo'));
    }

    public function testPullReturnsDefaultWhenMissing() {
        $repo = $this->getRepository();
        $this->assertSame('default', $repo->pull('foo', 'default'));
    }

    public function testAddStoresValueWhenKeyDoesNotExist() {
        $repo = $this->getRepository();
        $result = $repo->add('foo', 'bar', 10);
        $this->assertTrue($result);
        $this->assertSame('bar', $repo->get('foo'));
    }

    public function testAddDoesNotOverwriteExistingKey() {
        $repo = $this->getRepository();
        $repo->put('foo', 'bar', 10);
        $result = $repo->add('foo', 'baz', 10);
        $this->assertFalse($result);
        $this->assertSame('bar', $repo->get('foo'));
    }

    public function testAddWithZeroOrNegativeTtlReturnsFalse() {
        $repo = $this->getRepository();
        $result = $repo->add('foo', 'bar', 0);
        $this->assertFalse($result);
        $this->assertNull($repo->get('foo'));
    }

    public function testIncrementDelegatesToDriver() {
        $repo = $this->getRepository();
        $repo->put('foo', 1, 10);
        $this->assertSame(2, $repo->increment('foo'));
        $this->assertSame(2, $repo->get('foo'));
    }

    public function testIncrementByGivenAmount() {
        $repo = $this->getRepository();
        $repo->put('foo', 1, 10);
        $this->assertSame(6, $repo->increment('foo', 5));
    }

    public function testDecrementDelegatesToDriver() {
        $repo = $this->getRepository();
        $repo->put('foo', 5, 10);
        $this->assertSame(4, $repo->decrement('foo'));
        $this->assertSame(4, $repo->get('foo'));
    }

    public function testManyReturnsMultipleValuesWithNullForMissing() {
        $repo = $this->getRepository();
        $repo->put('foo', 'bar', 10);
        $repo->put('baz', 'qux', 10);

        $this->assertSame([
            'foo' => 'bar',
            'baz' => 'qux',
            'missing' => null,
        ], $repo->many(['foo', 'baz', 'missing']));
    }

    public function testGetWithArrayOfKeysDelegatesToMany() {
        $repo = $this->getRepository();
        $repo->put('foo', 'bar', 10);
        $repo->put('baz', 'qux', 10);

        $this->assertSame([
            'foo' => 'bar',
            'baz' => 'qux',
        ], $repo->get(['foo', 'baz']));
    }

    public function testManyWithDefaultsForMissingKeys() {
        $repo = $this->getRepository();
        $repo->put('foo', 'bar', 10);

        $this->assertSame([
            'foo' => 'bar',
            'missing' => 'default',
        ], $repo->many(['foo', 'missing' => 'default']));
    }

    public function testPutManyStoresMultipleValues() {
        $repo = $this->getRepository();
        $result = $repo->putMany(['foo' => 'bar', 'baz' => 'qux'], 10);
        $this->assertTrue($result);
        $this->assertSame('bar', $repo->get('foo'));
        $this->assertSame('qux', $repo->get('baz'));
    }

    public function testPutManyWithNullTtlStoresForever() {
        $repo = $this->getRepository();
        $repo->putMany(['foo' => 'bar', 'baz' => 'qux']);
        $this->assertSame('bar', $repo->get('foo'));
        $this->assertSame('qux', $repo->get('baz'));
    }

    public function testPutManyWithZeroOrNegativeTtlDeletesKeys() {
        $repo = $this->getRepository();
        $repo->put('foo', 'existing', 10);
        $repo->putMany(['foo' => 'bar'], 0);
        $this->assertNull($repo->get('foo'));
    }

    public function testRememberReturnsCachedValueIfPresent() {
        $repo = $this->getRepository();
        $repo->put('foo', 'bar', 10);

        $result = $repo->remember('foo', 10, function () {
            throw new RuntimeException('should not be called');
        });

        $this->assertSame('bar', $result);
    }

    public function testRememberStoresResultOfCallbackWhenMissing() {
        $repo = $this->getRepository();

        $result = $repo->remember('foo', 10, function () {
            return 'computed';
        });

        $this->assertSame('computed', $result);
        $this->assertSame('computed', $repo->get('foo'));
    }

    public function testRememberForeverStoresResultOfCallbackWhenMissing() {
        CCarbon::setTestNow(CCarbon::now());
        $repo = $this->getRepository();

        $result = $repo->rememberForever('foo', function () {
            return 'computed';
        });

        $this->assertSame('computed', $result);
        CCarbon::setTestNow(CCarbon::now()->addYears(100));
        $this->assertSame('computed', $repo->get('foo'));
        CCarbon::setTestNow(null);
    }

    public function testRememberForeverReturnsCachedValueIfPresent() {
        $repo = $this->getRepository();
        $repo->forever('foo', 'bar');

        $result = $repo->rememberForever('foo', function () {
            throw new RuntimeException('should not be called');
        });

        $this->assertSame('bar', $result);
    }

    public function testSearIsAliasForRememberForever() {
        $repo = $this->getRepository();

        $result = $repo->sear('foo', function () {
            return 'computed';
        });

        $this->assertSame('computed', $result);
        $this->assertSame('computed', $repo->get('foo'));
    }

    public function testClearFlushesTheDriver() {
        $repo = $this->getRepository();
        $repo->put('foo', 'bar', 10);
        $repo->put('baz', 'qux', 10);

        $result = $repo->clear();

        $this->assertTrue($result);
        $this->assertNull($repo->get('foo'));
        $this->assertNull($repo->get('baz'));
    }

    public function testGetDefaultCacheTimeDefaultsTo3600() {
        $repo = $this->getRepository();
        $this->assertSame(3600, $repo->getDefaultCacheTime());
    }

    public function testSetDefaultCacheTimeChangesDefault() {
        $repo = $this->getRepository();
        $result = $repo->setDefaultCacheTime(60);
        $this->assertSame($repo, $result);
        $this->assertSame(60, $repo->getDefaultCacheTime());
    }

    public function testGetDriverReturnsUnderlyingDriver() {
        $driver = new CCache_Driver_ArrayDriver();
        $repo = new CCache_Repository($driver);
        $this->assertSame($driver, $repo->getDriver());
    }

    public function testSupportsTagsReturnsTrueForTaggableDriver() {
        $repo = $this->getRepository();
        $this->assertTrue($repo->supportsTags());
    }

    public function testTagsReturnsATaggedCacheInstance() {
        $repo = $this->getRepository();
        $tagged = $repo->tags(['people', 'artists']);
        $this->assertInstanceOf(CCache_TaggedCache::class, $tagged);
    }

    public function testTaggedCacheIsolatesValuesByTag() {
        $repo = $this->getRepository();
        $repo->tags('people')->put('foo', 'bar', 10);
        $repo->tags('artists')->put('foo', 'baz', 10);

        $this->assertSame('bar', $repo->tags('people')->get('foo'));
        $this->assertSame('baz', $repo->tags('artists')->get('foo'));
        $this->assertNull($repo->get('foo'));
    }

    public function testArrayAccessOffsetExists() {
        $repo = $this->getRepository();
        $repo->put('foo', 'bar', 10);
        $this->assertTrue(isset($repo['foo']));
        $this->assertFalse(isset($repo['missing']));
    }

    public function testArrayAccessOffsetGet() {
        $repo = $this->getRepository();
        $repo->put('foo', 'bar', 10);
        $this->assertSame('bar', $repo['foo']);
    }

    public function testArrayAccessOffsetSetStoresForDefaultTime() {
        $repo = $this->getRepository();
        $repo['foo'] = 'bar';
        $this->assertSame('bar', $repo->get('foo'));
    }

    public function testArrayAccessOffsetUnsetRemovesItem() {
        $repo = $this->getRepository();
        $repo->put('foo', 'bar', 10);
        unset($repo['foo']);
        $this->assertNull($repo->get('foo'));
    }

    public function testCallDelegatesMissingMethodsToDriver() {
        $repo = $this->getRepository();
        $repo->put('foo', 1, 10);
        // getPrefix() is not defined on the repository, but is on the driver.
        $this->assertSame('', $repo->getPrefix());
    }

    public function testConstructingWithArrayOptionsResolvesArrayDriver() {
        $repo = new CCache_Repository(['driver' => 'Array']);
        $repo->put('foo', 'bar', 10);
        $this->assertSame('bar', $repo->get('foo'));
    }

    public function testCacheHitEventIsFired() {
        $repo = $this->getRepository();
        $repo->put('foo', 'bar', 10);

        $fired = null;
        CEvent::dispatcher()->listen(CCache_Event_CacheHit::class, function ($event) use (&$fired) {
            $fired = $event;
        });

        $repo->get('foo');

        $this->assertInstanceOf(CCache_Event_CacheHit::class, $fired);
        $this->assertSame('foo', $fired->key);
        $this->assertSame('bar', $fired->value);
    }

    public function testCacheMissedEventIsFired() {
        $repo = $this->getRepository();

        $fired = null;
        CEvent::dispatcher()->listen(CCache_Event_CacheMissed::class, function ($event) use (&$fired) {
            $fired = $event;
        });

        $repo->get('missing-key');

        $this->assertInstanceOf(CCache_Event_CacheMissed::class, $fired);
        $this->assertSame('missing-key', $fired->key);
    }

    public function testKeyWrittenEventIsFired() {
        $repo = $this->getRepository();

        $fired = null;
        CEvent::dispatcher()->listen(CCache_Event_KeyWritten::class, function ($event) use (&$fired) {
            $fired = $event;
        });

        $repo->put('foo', 'bar', 10);

        $this->assertInstanceOf(CCache_Event_KeyWritten::class, $fired);
        $this->assertSame('foo', $fired->key);
        $this->assertSame('bar', $fired->value);
        $this->assertSame(10, $fired->seconds);
    }

    public function testKeyForgottenEventIsFired() {
        $repo = $this->getRepository();
        $repo->put('foo', 'bar', 10);

        $fired = null;
        CEvent::dispatcher()->listen(CCache_Event_KeyForgotten::class, function ($event) use (&$fired) {
            $fired = $event;
        });

        $repo->forget('foo');

        $this->assertInstanceOf(CCache_Event_KeyForgotten::class, $fired);
        $this->assertSame('foo', $fired->key);
    }
}
