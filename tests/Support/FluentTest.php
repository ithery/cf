<?php

use PHPUnit\Framework\TestCase;

class FluentTest extends TestCase {
    public function testConstructedFromArray() {
        $fluent = new CBase_Fluent(['name' => 'Taylor', 'age' => 25]);

        $this->assertSame('Taylor', $fluent->get('name'));
        $this->assertSame(25, $fluent->get('age'));
    }

    public function testConstructedFromObject() {
        $object = new stdClass();
        $object->name = 'Taylor';
        $object->age = 25;

        $fluent = new CBase_Fluent($object);

        $this->assertSame('Taylor', $fluent->get('name'));
        $this->assertSame(25, $fluent->get('age'));
    }

    public function testConstructedWithNoArguments() {
        $fluent = new CBase_Fluent();

        $this->assertSame([], $fluent->getAttributes());
    }

    public function testGetReturnsDefaultWhenAttributeMissing() {
        $fluent = new CBase_Fluent();

        $this->assertNull($fluent->get('missing'));
        $this->assertSame('default', $fluent->get('missing', 'default'));
    }

    public function testGetResolvesClosureDefault() {
        $fluent = new CBase_Fluent();

        $this->assertSame('resolved', $fluent->get('missing', function () {
            return 'resolved';
        }));
    }

    public function testGetAttributesReturnsAllAttributes() {
        $fluent = new CBase_Fluent(['name' => 'Taylor', 'age' => 25]);

        $this->assertSame(['name' => 'Taylor', 'age' => 25], $fluent->getAttributes());
    }

    public function testToArray() {
        $fluent = new CBase_Fluent(['name' => 'Taylor', 'age' => 25]);

        $this->assertSame(['name' => 'Taylor', 'age' => 25], $fluent->toArray());
    }

    public function testJsonSerialize() {
        $fluent = new CBase_Fluent(['name' => 'Taylor', 'age' => 25]);

        $this->assertSame(['name' => 'Taylor', 'age' => 25], $fluent->jsonSerialize());
    }

    public function testToJson() {
        $fluent = new CBase_Fluent(['name' => 'Taylor', 'age' => 25]);

        $this->assertSame('{"name":"Taylor","age":25}', $fluent->toJson());
    }

    public function testArrayAccessOffsetExists() {
        $fluent = new CBase_Fluent(['name' => 'Taylor']);

        $this->assertTrue(isset($fluent['name']));
        $this->assertFalse(isset($fluent['missing']));
    }

    public function testArrayAccessOffsetGet() {
        $fluent = new CBase_Fluent(['name' => 'Taylor']);

        $this->assertSame('Taylor', $fluent['name']);
        $this->assertNull($fluent['missing']);
    }

    public function testArrayAccessOffsetSet() {
        $fluent = new CBase_Fluent();
        $fluent['name'] = 'Taylor';

        $this->assertSame('Taylor', $fluent['name']);
    }

    public function testArrayAccessOffsetUnset() {
        $fluent = new CBase_Fluent(['name' => 'Taylor']);
        unset($fluent['name']);

        $this->assertFalse(isset($fluent['name']));
    }

    public function testDynamicCallSetsAttributeAndReturnsSelfForChaining() {
        $fluent = new CBase_Fluent();

        $result = $fluent->name('Taylor');

        $this->assertSame($fluent, $result);
        $this->assertSame('Taylor', $fluent->name);
    }

    public function testDynamicCallWithoutArgumentsSetsTrue() {
        $fluent = new CBase_Fluent();

        $fluent->nullable();

        $this->assertTrue($fluent->nullable);
    }

    public function testDynamicCallSupportsChaining() {
        $fluent = new CBase_Fluent();

        $fluent->name('Taylor')->age(25)->nullable();

        $this->assertSame([
            'name' => 'Taylor',
            'age' => 25,
            'nullable' => true,
        ], $fluent->getAttributes());
    }

    public function testMagicGetSetIssetUnset() {
        $fluent = new CBase_Fluent();

        $fluent->name = 'Taylor';

        $this->assertSame('Taylor', $fluent->name);
        $this->assertTrue(isset($fluent->name));

        unset($fluent->name);

        $this->assertFalse(isset($fluent->name));
        $this->assertNull($fluent->name);
    }
}
