<?php
use PHPUnit\Framework\TestCase;

class CacheArrayDriverTest extends TestCase {
    public function testItemsCanBeSetAndRetrieved() {
        $store = new CCache_Driver_ArrayDriver();
        $result = $store->put('foo', 'bar', 10);
        $this->assertTrue($result);
        $this->assertSame('bar', $store->get('foo'));
    }

    public function testMultipleItemsCanBeSetAndRetrieved() {
        $store = new CCache_Driver_ArrayDriver();
        $result = $store->put('foo', 'bar', 10);
        $resultMany = $store->putMany([
            'fizz' => 'buz',
            'quz' => 'baz',
        ], 10);
        $this->assertTrue($result);
        $this->assertTrue($resultMany);
        $this->assertEquals([
            'foo' => 'bar',
            'fizz' => 'buz',
            'quz' => 'baz',
            'norf' => null,
        ], $store->many(['foo', 'fizz', 'quz', 'norf']));
    }

    public function testItemsCanExpire() {
        CCarbon::setTestNow(CCarbon::now());
        $store = new CCache_Driver_ArrayDriver();

        $store->put('foo', 'bar', 10);
        CCarbon::setTestNow(CCarbon::now()->addSeconds(10)->addSecond());
        $result = $store->get('foo');

        $this->assertNull($result);
        CCarbon::setTestNow(null);
    }

    public function testStoreItemForeverProperlyStoresInArray() {
        $mock = $this->getMockBuilder(CCache_Driver_ArrayDriver::class)->onlyMethods(['put'])->getMock();
        $mock->expects($this->once())
            ->method('put')->with($this->equalTo('foo'), $this->equalTo('bar'), $this->equalTo(0))
            ->willReturn(true);
        $result = $mock->forever('foo', 'bar');
        $this->assertTrue($result);
    }

    public function testValuesCanBeIncremented() {
        $store = new CCache_Driver_ArrayDriver();
        $store->put('foo', 1, 10);
        $result = $store->increment('foo');
        $this->assertEquals(2, $result);
        $this->assertEquals(2, $store->get('foo'));
    }

    public function testNonExistingKeysCanBeIncremented() {
        $store = new CCache_Driver_ArrayDriver();
        $result = $store->increment('foo');
        $this->assertEquals(1, $result);
        $this->assertEquals(1, $store->get('foo'));
    }

    public function testExpiredKeysAreIncrementedLikeNonExistingKeys() {
        CCarbon::setTestNow(CCarbon::now());
        $store = new CCache_Driver_ArrayDriver();

        $store->put('foo', 999, 10);
        CCarbon::setTestNow(CCarbon::now()->addSeconds(10)->addSecond());
        $result = $store->increment('foo');

        $this->assertEquals(1, $result);
        CCarbon::setTestNow(null);
    }

    public function testValuesCanBeDecremented() {
        $store = new CCache_Driver_ArrayDriver();
        $store->put('foo', 1, 10);
        $result = $store->decrement('foo');
        $this->assertEquals(0, $result);
        $this->assertEquals(0, $store->get('foo'));
    }

    public function testItemsCanBeRemoved() {
        $store = new CCache_Driver_ArrayDriver();
        $store->put('foo', 'bar', 10);
        $this->assertTrue($store->forget('foo'));
        $this->assertNull($store->get('foo'));
        $this->assertFalse($store->forget('foo'));
    }

    public function testItemsCanBeFlushed() {
        $store = new CCache_Driver_ArrayDriver();
        $store->put('foo', 'bar', 10);
        $store->put('baz', 'boom', 10);
        $result = $store->flush();
        $this->assertTrue($result);
        $this->assertNull($store->get('foo'));
        $this->assertNull($store->get('baz'));
    }

    public function testCacheKey() {
        $store = new CCache_Driver_ArrayDriver();
        $this->assertEmpty($store->getPrefix());
    }

    public function testCannotAcquireLockTwice() {
        $store = new CCache_Driver_ArrayDriver();
        $lock = $store->lock('foo', 10);

        $this->assertTrue($lock->acquire());
        $this->assertFalse($lock->acquire());
    }

    public function testCanAcquireLockAgainAfterExpiry() {
        CCarbon::setTestNow(CCarbon::now());
        $store = new CCache_Driver_ArrayDriver();
        $lock = $store->lock('foo', 10);
        $lock->acquire();
        CCarbon::setTestNow(CCarbon::now()->addSeconds(10));

        $this->assertTrue($lock->acquire());
    }

    public function testLockExpirationLowerBoundary() {
        CCarbon::setTestNow(CCarbon::now());
        $store = new CCache_Driver_ArrayDriver();
        $lock = $store->lock('foo', 10);
        $lock->acquire();
        CCarbon::setTestNow(CCarbon::now()->addSeconds(10)->subMicrosecond());

        $this->assertFalse($lock->acquire());
    }

    public function testLockWithNoExpirationNeverExpires() {
        CCarbon::setTestNow(CCarbon::now());
        $store = new CCache_Driver_ArrayDriver();
        $lock = $store->lock('foo');
        $lock->acquire();
        CCarbon::setTestNow(CCarbon::now()->addYears(100));

        $this->assertFalse($lock->acquire());
    }

    public function testCanAcquireLockAfterRelease() {
        $store = new CCache_Driver_ArrayDriver();
        $lock = $store->lock('foo', 10);
        $lock->acquire();

        $this->assertTrue($lock->release());
        $this->assertTrue($lock->acquire());
    }

    public function testAnotherOwnerCannotReleaseLock() {
        $store = new CCache_Driver_ArrayDriver();
        $owner = $store->lock('foo', 10);
        $wannabeOwner = $store->lock('foo', 10);
        $owner->acquire();

        $this->assertFalse($wannabeOwner->release());
    }

    public function testAnotherOwnerCanForceReleaseALock() {
        $store = new CCache_Driver_ArrayDriver();
        $owner = $store->lock('foo', 10);
        $wannabeOwner = $store->lock('foo', 10);
        $owner->acquire();
        $wannabeOwner->forceRelease();

        $this->assertTrue($wannabeOwner->acquire());
    }

    public function testValuesAreNotStoredByReference() {
        $store = new CCache_Driver_ArrayDriver($serialize = true);
        $object = new stdClass();
        $object->foo = true;

        $store->put('object', $object, 10);
        $object->bar = true;

        $this->assertObjectNotHasAttribute('bar', $store->get('object'));
    }

    public function testValuesAreStoredByReferenceIfSerializationIsDisabled() {
        $store = new CCache_Driver_ArrayDriver();
        $object = new stdClass();
        $object->foo = true;

        $store->put('object', $object, 10);
        $object->bar = true;

        $this->assertObjectHasAttribute('bar', $store->get('object'));
    }

    public function testReleasingLockAfterAlreadyForceReleasedByAnotherOwnerFails() {
        $store = new CCache_Driver_ArrayDriver();
        $owner = $store->lock('foo', 10);
        $wannabeOwner = $store->lock('foo', 10);
        $owner->acquire();
        $wannabeOwner->forceRelease();

        $this->assertFalse($wannabeOwner->release());
    }
}
