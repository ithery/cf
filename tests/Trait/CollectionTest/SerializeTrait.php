<?php
use Mockery as m;

trait CollectionTest_SerializeTrait {
    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testJsonSerialize($collection) {
        $c = new $collection([
            new TestArrayableObject(),
            new TestJsonableObject(),
            new TestJsonSerializeObject(),
            'baz',
        ]);

        $this->assertSame([
            ['foo' => 'bar'],
            ['foo' => 'bar'],
            ['foo' => 'bar'],
            'baz',
        ], $c->jsonSerialize());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testGetArrayableItems($collection) {
        $data = new $collection();

        $class = new ReflectionClass($collection);
        $method = $class->getMethod('getArrayableItems');
        $method->setAccessible(true);

        $items = new TestArrayableObject();
        $array = $method->invokeArgs($data, [$items]);
        $this->assertSame(['foo' => 'bar'], $array);

        $items = new TestJsonableObject();
        $array = $method->invokeArgs($data, [$items]);
        $this->assertSame(['foo' => 'bar'], $array);

        $items = new TestJsonSerializeObject();
        $array = $method->invokeArgs($data, [$items]);
        $this->assertSame(['foo' => 'bar'], $array);

        $items = new TestJsonSerializeWithScalarValueObject();
        $array = $method->invokeArgs($data, [$items]);
        $this->assertSame(['foo'], $array);

        $items = new $collection(['foo' => 'bar']);
        $array = $method->invokeArgs($data, [$items]);
        $this->assertSame(['foo' => 'bar'], $array);

        $items = ['foo' => 'bar'];
        $array = $method->invokeArgs($data, [$items]);
        $this->assertSame(['foo' => 'bar'], $array);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testToArrayCallsToArrayOnEachItemInCollection($collection) {
        $item1 = m::mock(Arrayable::class);
        $item1->shouldReceive('toArray')->once()->andReturn('foo.array');
        $item2 = m::mock(Arrayable::class);
        $item2->shouldReceive('toArray')->once()->andReturn('bar.array');
        $c = new $collection([$item1, $item2]);
        $results = $c->toArray();

        $this->assertEquals(['foo.array', 'bar.array'], $results);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testJsonSerializeCallsToArrayOrJsonSerializeOnEachItemInCollection($collection) {
        $item1 = m::mock(JsonSerializable::class);
        $item1->shouldReceive('jsonSerialize')->once()->andReturn('foo.json');
        $item2 = m::mock(Arrayable::class);
        $item2->shouldReceive('toArray')->once()->andReturn('bar.array');
        $c = new $collection([$item1, $item2]);
        $results = $c->jsonSerialize();

        $this->assertEquals(['foo.json', 'bar.array'], $results);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testToJsonEncodesTheJsonSerializeResult($collection) {
        $c = $this->getMockBuilder($collection)->onlyMethods(['jsonSerialize'])->getMock();
        $c->expects($this->once())->method('jsonSerialize')->willReturn('foo');
        $results = $c->toJson();
        $this->assertJsonStringEqualsJsonString(json_encode('foo'), $results);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testCastingToStringJsonEncodesTheToArrayResult($collection) {
        $c = $this->getMockBuilder($collection)->onlyMethods(['jsonSerialize'])->getMock();
        $c->expects($this->once())->method('jsonSerialize')->willReturn('foo');

        $this->assertJsonStringEqualsJsonString(json_encode('foo'), (string) $c);
    }

    public function testBehavesLikeAnArrayWithArrayAccess() {
        // indexed array
        $input = ['foo', null];
        $c = new CCollection($input);
        $this->assertEquals(isset($input[0]), isset($c[0])); // existing value
        $this->assertEquals(isset($input[1]), isset($c[1])); // existing but null value
        $this->assertEquals(isset($input[1000]), isset($c[1000])); // non-existing value
        $this->assertEquals($input[0], $c[0]);
        $this->assertEquals($input[1], $c[1]);

        // associative array
        $input = ['k1' => 'foo', 'k2' => null];
        $c = new CCollection($input);
        $this->assertEquals(isset($input['k1']), isset($c['k1'])); // existing value
        $this->assertEquals(isset($input['k2']), isset($c['k2'])); // existing but null value
        $this->assertEquals(isset($input['k3']), isset($c['k3'])); // non-existing value
        $this->assertEquals($input['k1'], $c['k1']);
        $this->assertEquals($input['k2'], $c['k2']);
    }

    public function testArrayAccessOffsetGet() {
        $c = new CCollection(['foo', 'bar']);
        $this->assertSame('foo', $c->offsetGet(0));
        $this->assertSame('bar', $c->offsetGet(1));
    }

    public function testArrayAccessOffsetSet() {
        $c = new CCollection(['foo', 'foo']);

        $c->offsetSet(1, 'bar');
        $this->assertSame('bar', $c[1]);

        $c->offsetSet(null, 'qux');
        $this->assertSame('qux', $c[2]);
    }

    public function testArrayAccessOffsetUnset() {
        $c = new CCollection(['foo', 'bar']);

        $c->offsetUnset(1);
        $this->assertFalse(isset($c[1]));
    }
}
