<?php

use PHPUnit\Framework\TestCase;

class OptionalTest extends TestCase {
    public function testGetExistItemOnObject() {
        $expected = 'test';

        $targetObj = new stdClass();
        $targetObj->item = $expected;

        $optional = new COptional($targetObj);

        $this->assertEquals($expected, $optional->item);
    }

    public function testGetNotExistItemOnObject() {
        $targetObj = new stdClass();

        $optional = new COptional($targetObj);

        $this->assertNull($optional->item);
    }

    public function testIssetExistItemOnObject() {
        $targetObj = new stdClass();
        $targetObj->item = '';

        $optional = new COptional($targetObj);

        $this->assertTrue(isset($optional->item));
    }

    public function testIssetNotExistItemOnObject() {
        $targetObj = new stdClass();

        $optional = new COptional($targetObj);

        $this->assertFalse(isset($optional->item));
    }

    public function testGetExistItemOnArray() {
        $expected = 'test';

        $targetArr = [
            'item' => $expected,
        ];

        $optional = new COptional($targetArr);

        $this->assertEquals($expected, $optional['item']);
    }

    public function testGetNotExistItemOnArray() {
        $targetObj = [];

        $optional = new COptional($targetObj);

        $this->assertNull($optional['item']);
    }

    public function testIssetExistItemOnArray() {
        $targetArr = [
            'item' => '',
        ];

        $optional = new COptional($targetArr);

        $this->assertTrue(isset($optional['item']));
        $this->assertTrue(isset($optional->item));
    }

    public function testIssetNotExistItemOnArray() {
        $targetArr = [];

        $optional = new COptional($targetArr);

        $this->assertFalse(isset($optional['item']));
        $this->assertFalse(isset($optional->item));
    }

    public function testIssetExistItemOnNull() {
        $targetNull = null;

        $optional = new COptional($targetNull);

        $this->assertFalse(isset($optional->item));
    }

    public function testCreateReturnsOptionalInstance() {
        $optional = COptional::create('test');

        $this->assertInstanceOf(COptional::class, $optional);
    }

    public function testOffsetSetOnArray() {
        $targetArr = ['item' => 'foo'];

        $optional = new COptional($targetArr);
        $optional['item'] = 'bar';

        $this->assertSame('bar', $optional['item']);
    }

    public function testOffsetSetOnArrayAddsNewKey() {
        $optional = new COptional([]);
        $optional['item'] = 'baz';

        $this->assertSame('baz', $optional['item']);
    }

    public function testOffsetSetOnNonAccessibleValueDoesNothing() {
        $optional = new COptional(new stdClass());
        $optional['item'] = 'should-not-be-set';

        $this->assertFalse(isset($optional['item']));
    }

    public function testOffsetUnsetOnArray() {
        $targetArr = ['item' => 'foo'];

        $optional = new COptional($targetArr);
        unset($optional['item']);

        $this->assertFalse(isset($optional['item']));
    }

    public function testCallMethodOnObject() {
        $optional = new COptional(new TestOptionalGreeter());

        $this->assertSame('Hello, World', $optional->greet('World'));
    }

    public function testCallMethodOnNullReturnsNull() {
        $optional = new COptional(null);

        $this->assertNull($optional->greet('World'));
    }
}
// @codingStandardsIgnoreStart
class TestOptionalGreeter {
    /**
     * @param string $name
     *
     * @return string
     */
    public function greet($name) {
        return 'Hello, ' . $name;
    }
}
