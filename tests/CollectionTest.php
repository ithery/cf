<?php

use PHPUnit\Framework\TestCase;
use Illuminate\Contracts\Support\Jsonable;
use Symfony\Component\VarDumper\VarDumper;
use Illuminate\Contracts\Support\Arrayable;

require_once dirname(__FILE__) . '/Trait/CollectionTest/BasicTrait.php';
require_once dirname(__FILE__) . '/Trait/CollectionTest/ExceptionTrait.php';

class CollectionTest extends TestCase {
    use CollectionTest_BasicTrait;
    use CollectionTest_ExceptionTrait;

    public function testPopReturnsAndRemovesLastItemInCollection() {
        $c = new CCollection(['foo', 'bar']);

        $this->assertSame('bar', $c->pop());
        $this->assertSame('foo', $c->first());
    }

    public function testPopReturnsAndRemovesLastXItemsInCollection() {
        $c = new CCollection(['foo', 'bar', 'baz']);

        $this->assertEquals(new CCollection(['baz', 'bar']), $c->pop(2));
        $this->assertSame('foo', $c->first());

        $this->assertEquals(new CCollection(['baz', 'bar', 'foo']), (new CCollection(['foo', 'bar', 'baz']))->pop(6));
    }

    public function testShiftReturnsAndRemovesFirstItemInCollection() {
        $data = new CCollection(['Taylor', 'Otwell']);

        $this->assertSame('Taylor', $data->shift());
        $this->assertSame('Otwell', $data->first());
        $this->assertSame('Otwell', $data->shift());
        $this->assertNull($data->first());
    }

    public function testShiftReturnsAndRemovesFirstXItemsInCollection() {
        $data = new CCollection(['foo', 'bar', 'baz']);

        $this->assertEquals(new CCollection(['foo', 'bar']), $data->shift(2));
        $this->assertSame('baz', $data->first());

        $this->assertEquals(new CCollection(['foo', 'bar', 'baz']), (new CCollection(['foo', 'bar', 'baz']))->shift(6));
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testSliding($collection) {
        // Default parameters: $size = 2, $step = 1
        $this->assertSame([], $collection::times(0)->sliding()->toArray());
        $this->assertSame([], $collection::times(1)->sliding()->toArray());
        $this->assertSame([[1, 2]], $collection::times(2)->sliding()->toArray());
        $this->assertSame(
            [[1, 2], [2, 3]],
            $collection::times(3)->sliding()->map->values()->toArray()
        );

        // Custom step: $size = 2, $step = 3
        $this->assertSame([], $collection::times(1)->sliding(2, 3)->toArray());
        $this->assertSame([[1, 2]], $collection::times(2)->sliding(2, 3)->toArray());
        $this->assertSame([[1, 2]], $collection::times(3)->sliding(2, 3)->toArray());
        $this->assertSame([[1, 2]], $collection::times(4)->sliding(2, 3)->toArray());
        $this->assertSame(
            [[1, 2], [4, 5]],
            $collection::times(5)->sliding(2, 3)->map->values()->toArray()
        );

        // Custom size: $size = 3, $step = 1
        $this->assertSame([], $collection::times(2)->sliding(3)->toArray());
        $this->assertSame([[1, 2, 3]], $collection::times(3)->sliding(3)->toArray());
        $this->assertSame(
            [[1, 2, 3], [2, 3, 4]],
            $collection::times(4)->sliding(3)->map->values()->toArray()
        );
        $this->assertSame(
            [[1, 2, 3], [2, 3, 4]],
            $collection::times(4)->sliding(3)->map->values()->toArray()
        );

        // Custom size and custom step: $size = 3, $step = 2
        $this->assertSame([], $collection::times(2)->sliding(3, 2)->toArray());
        $this->assertSame([[1, 2, 3]], $collection::times(3)->sliding(3, 2)->toArray());
        $this->assertSame([[1, 2, 3]], $collection::times(4)->sliding(3, 2)->toArray());
        $this->assertSame(
            [[1, 2, 3], [3, 4, 5]],
            $collection::times(5)->sliding(3, 2)->map->values()->toArray()
        );
        $this->assertSame(
            [[1, 2, 3], [3, 4, 5]],
            $collection::times(6)->sliding(3, 2)->map->values()->toArray()
        );

        // Ensure keys are preserved, and inner chunks are also collections
        $chunks = $collection::times(3)->sliding();

        $this->assertSame([[0 => 1, 1 => 2], [1 => 2, 2 => 3]], $chunks->toArray());

        $this->assertInstanceOf($collection, $chunks);
        $this->assertInstanceOf($collection, $chunks->first());
        $this->assertInstanceOf($collection, $chunks->skip(1)->first());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testEmptyCollectionIsEmpty($collection) {
        $c = new $collection();

        $this->assertTrue($c->isEmpty());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testEmptyCollectionIsNotEmpty($collection) {
        $c = new $collection(['foo', 'bar']);

        $this->assertFalse($c->isEmpty());
        $this->assertTrue($c->isNotEmpty());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testCollectionIsConstructed($collection) {
        $data = new $collection('foo');
        $this->assertSame(['foo'], $data->all());

        $data = new $collection(2);
        $this->assertSame([2], $data->all());

        $data = new $collection(false);
        $this->assertSame([false], $data->all());

        $data = new $collection(null);
        $this->assertEmpty($data->all());

        $data = new $collection();
        $this->assertEmpty($data->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testCollectionShuffleWithSeed($collection) {
        $data = new $collection(range(0, 100, 10));

        $firstRandom = $data->shuffle(1234);
        $secondRandom = $data->shuffle(1234);

        $this->assertEquals($firstRandom, $secondRandom);
    }

    public function testOffsetAccess() {
        $c = new CCollection(['name' => 'ither']);
        $this->assertSame('ither', $c['name']);
        $c['name'] = 'dayle';
        $this->assertSame('dayle', $c['name']);
        $this->assertTrue(isset($c['name']));
        unset($c['name']);
        $this->assertFalse(isset($c['name']));
        $c[] = 'jason';
        $this->assertSame('jason', $c[0]);
    }

    public function testArrayAccessOffsetExists() {
        $c = new CCollection(['foo', 'bar', null]);
        $this->assertTrue($c->offsetExists(0));
        $this->assertTrue($c->offsetExists(1));
        $this->assertFalse($c->offsetExists(2));
    }

    public function testForgetSingleKey() {
        $c = new CCollection(['foo', 'bar']);
        $c = $c->forget(0)->all();
        $this->assertFalse(isset($c['foo']));
        $this->assertFalse(isset($c[0]));
        $this->assertTrue(isset($c[1]));

        $c = new CCollection(['foo' => 'bar', 'baz' => 'qux']);
        $c = $c->forget('foo')->all();
        $this->assertFalse(isset($c['foo']));
        $this->assertTrue(isset($c['baz']));
    }

    public function testForgetArrayOfKeys() {
        $c = new CCollection(['foo', 'bar', 'baz']);
        $c = $c->forget([0, 2])->all();
        $this->assertFalse(isset($c[0]));
        $this->assertFalse(isset($c[2]));
        $this->assertTrue(isset($c[1]));

        $c = new CCollection(['name' => 'ither', 'foo' => 'bar', 'baz' => 'qux']);
        $c = $c->forget(['foo', 'baz'])->all();
        $this->assertFalse(isset($c['foo']));
        $this->assertFalse(isset($c['baz']));
        $this->assertTrue(isset($c['name']));
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testCountable($collection) {
        $c = new $collection(['foo', 'bar']);
        $this->assertCount(2, $c);
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testCountByStandalone($collection) {
        $c = new $collection(['foo', 'foo', 'foo', 'bar', 'bar', 'foobar']);
        $this->assertEquals(['foo' => 3, 'bar' => 2, 'foobar' => 1], $c->countBy()->all());

        $c = new $collection([true, true, false, false, false]);
        $this->assertEquals([true => 2, false => 3], $c->countBy()->all());

        $c = new $collection([1, 5, 1, 5, 5, 1]);
        $this->assertEquals([1 => 3, 5 => 3], $c->countBy()->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testCountByWithKey($collection) {
        $c = new $collection([
            ['key' => 'a'], ['key' => 'a'], ['key' => 'a'], ['key' => 'a'],
            ['key' => 'b'], ['key' => 'b'], ['key' => 'b'],
        ]);
        $this->assertEquals(['a' => 4, 'b' => 3], $c->countBy('key')->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testCountableByWithCallback($collection) {
        $c = new $collection(['alice', 'aaron', 'bob', 'carla']);
        $this->assertEquals(['a' => 2, 'b' => 1, 'c' => 1], $c->countBy(function ($name) {
            return substr($name, 0, 1);
        })->all());

        $c = new $collection([1, 2, 3, 4, 5]);
        $this->assertEquals([true => 2, false => 3], $c->countBy(function ($i) {
            return $i % 2 === 0;
        })->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testContainsOneItem($collection) {
        $this->assertFalse((new $collection([]))->containsOneItem());
        $this->assertTrue((new $collection([1]))->containsOneItem());
        $this->assertFalse((new $collection([1, 2]))->containsOneItem());
    }

    public function testIterable() {
        $c = new CCollection(['foo']);
        $this->assertInstanceOf(ArrayIterator::class, $c->getIterator());
        $this->assertEquals(['foo'], $c->getIterator()->getArrayCopy());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testCachingIterator($collection) {
        $c = new $collection(['foo']);
        $this->assertInstanceOf(CachingIterator::class, $c->getCachingIterator());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testChunk($collection) {
        $data = new $collection([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $data = $data->chunk(3);

        $this->assertInstanceOf($collection, $data);
        $this->assertInstanceOf($collection, $data->first());
        $this->assertCount(4, $data);
        $this->assertEquals([1, 2, 3], $data->first()->toArray());
        $this->assertEquals([9 => 10], $data->get(3)->toArray());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testChunkWhenGivenZeroAsSize($collection) {
        $data = new $collection([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        $this->assertEquals(
            [],
            $data->chunk(0)->toArray()
        );
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testChunkWhenGivenLessThanZero($collection) {
        $data = new $collection([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        $this->assertEquals(
            [],
            $data->chunk(-1)->toArray()
        );
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testSplitIn($collection) {
        $data = new $collection([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $data = $data->splitIn(3);

        $this->assertInstanceOf($collection, $data);
        $this->assertInstanceOf($collection, $data->first());
        $this->assertCount(3, $data);
        $this->assertEquals([1, 2, 3, 4], $data->get(0)->values()->toArray());
        $this->assertEquals([5, 6, 7, 8], $data->get(1)->values()->toArray());
        $this->assertEquals([9, 10], $data->get(2)->values()->toArray());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testChunkWhileOnEqualElements($collection) {
        $data = (new $collection(['A', 'A', 'B', 'B', 'C', 'C', 'C']))
            ->chunkWhile(function ($current, $key, $chunk) {
                return $chunk->last() === $current;
            });

        $this->assertInstanceOf($collection, $data);
        $this->assertInstanceOf($collection, $data->first());
        $this->assertEquals([0 => 'A', 1 => 'A'], $data->first()->toArray());
        $this->assertEquals([2 => 'B', 3 => 'B'], $data->get(1)->toArray());
        $this->assertEquals([4 => 'C', 5 => 'C', 6 => 'C'], $data->last()->toArray());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testChunkWhileOnContiguouslyIncreasingIntegers($collection) {
        $data = (new $collection([1, 4, 9, 10, 11, 12, 15, 16, 19, 20, 21]))
            ->chunkWhile(function ($current, $key, $chunk) {
                return $chunk->last() + 1 == $current;
            });

        $this->assertInstanceOf($collection, $data);
        $this->assertInstanceOf($collection, $data->first());
        $this->assertEquals([0 => 1], $data->first()->toArray());
        $this->assertEquals([1 => 4], $data->get(1)->toArray());
        $this->assertEquals([2 => 9, 3 => 10, 4 => 11, 5 => 12], $data->get(2)->toArray());
        $this->assertEquals([6 => 15, 7 => 16], $data->get(3)->toArray());
        $this->assertEquals([8 => 19, 9 => 20, 10 => 21], $data->last()->toArray());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testEvery($collection) {
        $c = new $collection([]);
        $this->assertTrue($c->every('key', 'value'));
        $this->assertTrue($c->every(function () {
            return false;
        }));

        $c = new $collection([['age' => 18], ['age' => 20], ['age' => 20]]);
        $this->assertFalse($c->every('age', 18));
        $this->assertTrue($c->every('age', '>=', 18));
        $this->assertTrue($c->every(function ($item) {
            return $item['age'] >= 18;
        }));
        $this->assertFalse($c->every(function ($item) {
            return $item['age'] >= 20;
        }));

        $c = new $collection([null, null]);
        $this->assertTrue($c->every(function ($item) {
            return $item === null;
        }));

        $c = new $collection([['active' => true], ['active' => true]]);
        $this->assertTrue($c->every('active'));
        $this->assertTrue($c->every->active);
        $this->assertFalse($c->concat([['active' => false]])->every->active);
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testExcept($collection) {
        $data = new $collection(['first' => 'Taylor', 'last' => 'Otwell', 'email' => 'cresenity@gmail.com']);

        $this->assertEquals(['first' => 'Taylor'], $data->except(['last', 'email', 'missing'])->all());
        $this->assertEquals(['first' => 'Taylor'], $data->except('last', 'email', 'missing')->all());

        $this->assertEquals(['first' => 'Taylor'], $data->except(c::collect(['last', 'email', 'missing']))->all());
        $this->assertEquals(['first' => 'Taylor', 'email' => 'cresenity@gmail.com'], $data->except(['last'])->all());
        $this->assertEquals(['first' => 'Taylor', 'email' => 'cresenity@gmail.com'], $data->except('last')->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testExceptSelf($collection) {
        $data = new $collection(['first' => 'Taylor', 'last' => 'Otwell']);
        $this->assertEquals(['first' => 'Taylor', 'last' => 'Otwell'], $data->except($data)->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testPluckWithArrayAndObjectValues($collection) {
        $data = new $collection([(object) ['name' => 'ither', 'email' => 'foo'], ['name' => 'dayle', 'email' => 'bar']]);
        $this->assertEquals(['ither' => 'foo', 'dayle' => 'bar'], $data->pluck('email', 'name')->all());
        $this->assertEquals(['foo', 'bar'], $data->pluck('email')->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testPluckWithArrayAccessValues($collection) {
        $data = new $collection([
            new TestArrayAccessImplementation(['name' => 'ither', 'email' => 'foo']),
            new TestArrayAccessImplementation(['name' => 'dayle', 'email' => 'bar']),
        ]);

        $this->assertEquals(['ither' => 'foo', 'dayle' => 'bar'], $data->pluck('email', 'name')->all());
        $this->assertEquals(['foo', 'bar'], $data->pluck('email')->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testHas($collection) {
        $data = new $collection(['id' => 1, 'first' => 'Hello', 'second' => 'World']);
        $this->assertTrue($data->has('first'));
        $this->assertFalse($data->has('third'));
        $this->assertTrue($data->has(['first', 'second']));
        $this->assertFalse($data->has(['third', 'first']));
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testHasAny($collection) {
        $data = new $collection(['id' => 1, 'first' => 'Hello', 'second' => 'World']);

        $this->assertTrue($data->hasAny('first'));
        $this->assertFalse($data->hasAny('third'));
        $this->assertTrue($data->hasAny(['first', 'second']));
        $this->assertTrue($data->hasAny(['first', 'fourth']));
        $this->assertFalse($data->hasAny(['third', 'fourth']));
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testImplode($collection) {
        $data = new $collection([['name' => 'ither', 'email' => 'foo'], ['name' => 'dayle', 'email' => 'bar']]);
        $this->assertSame('foobar', $data->implode('email'));
        $this->assertSame('foo,bar', $data->implode('email', ','));
        $data = new $collection(['ither', 'dayle']);
        $this->assertSame('itherdayle', $data->implode(''));
        $this->assertSame('ither,dayle', $data->implode(','));

        $data = new $collection([
            ['name' => cstr::of('ither'), 'email' => cstr::of('foo')],
            ['name' => cstr::of('dayle'), 'email' => cstr::of('bar')],
        ]);
        $this->assertSame('foobar', $data->implode('email'));
        $this->assertSame('foo,bar', $data->implode('email', ','));

        $data = new $collection([cstr::of('ither'), cstr::of('dayle')]);

        $this->assertSame('itherdayle', $data->implode(''));
        $this->assertSame('ither,dayle', $data->implode(','));
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testTake($collection) {
        $data = new $collection(['ither', 'dayle', 'shawn']);
        $data = $data->take(2);
        $this->assertEquals(['ither', 'dayle'], $data->all());
    }

    public function testPut() {
        $data = new CCollection(['name' => 'ither', 'email' => 'foo']);
        $data = $data->put('name', 'dayle');
        $this->assertEquals(['name' => 'dayle', 'email' => 'foo'], $data->all());
    }

    public function testPutWithNoKey() {
        $data = new CCollection(['ither', 'shawn']);
        $data = $data->put(null, 'dayle');
        $this->assertEquals(['ither', 'shawn', 'dayle'], $data->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testRandom($collection) {
        $data = new $collection([1, 2, 3, 4, 5, 6]);

        $random = $data->random();
        $this->assertIsInt($random);
        $this->assertContains($random, $data->all());

        $random = $data->random(0);
        $this->assertInstanceOf($collection, $random);
        $this->assertCount(0, $random);

        $random = $data->random(1);
        $this->assertInstanceOf($collection, $random);
        $this->assertCount(1, $random);

        $random = $data->random(2);
        $this->assertInstanceOf($collection, $random);
        $this->assertCount(2, $random);

        $random = $data->random('0');
        $this->assertInstanceOf($collection, $random);
        $this->assertCount(0, $random);

        $random = $data->random('1');
        $this->assertInstanceOf($collection, $random);
        $this->assertCount(1, $random);

        $random = $data->random('2');
        $this->assertInstanceOf($collection, $random);
        $this->assertCount(2, $random);
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testRandomOnEmptyCollection($collection) {
        $data = new $collection();

        $random = $data->random(0);
        $this->assertInstanceOf($collection, $random);
        $this->assertCount(0, $random);

        $random = $data->random('0');
        $this->assertInstanceOf($collection, $random);
        $this->assertCount(0, $random);
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testTakeLast($collection) {
        $data = new $collection(['ither', 'dayle', 'shawn']);
        $data = $data->take(-2);
        $this->assertEquals([1 => 'dayle', 2 => 'shawn'], $data->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testTakeUntilUsingValue($collection) {
        $data = new $collection([1, 2, 3, 4]);

        $data = $data->takeUntil(3);

        $this->assertSame([1, 2], $data->toArray());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testTakeUntilUsingCallback($collection) {
        $data = new $collection([1, 2, 3, 4]);

        $data = $data->takeUntil(function ($item) {
            return $item >= 3;
        });

        $this->assertSame([1, 2], $data->toArray());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testTakeUntilReturnsAllItemsForUnmetValue($collection) {
        $data = new $collection([1, 2, 3, 4]);

        $actual = $data->takeUntil(99);

        $this->assertSame($data->toArray(), $actual->toArray());

        $actual = $data->takeUntil(function ($item) {
            return $item >= 99;
        });

        $this->assertSame($data->toArray(), $actual->toArray());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testTakeUntilCanBeProxied($collection) {
        $data = new $collection([
            new TestSupportCollectionHigherOrderItem('Adam'),
            new TestSupportCollectionHigherOrderItem('Taylor'),
            new TestSupportCollectionHigherOrderItem('Jason'),
        ]);

        $actual = $data->takeUntil->is('Jason');

        $this->assertCount(2, $actual);
        $this->assertSame('Adam', $actual->get(0)->name);
        $this->assertSame('Taylor', $actual->get(1)->name);
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testTakeWhileUsingValue($collection) {
        $data = new $collection([1, 1, 2, 2, 3, 3]);

        $data = $data->takeWhile(1);

        $this->assertSame([1, 1], $data->toArray());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testTakeWhileUsingCallback($collection) {
        $data = new $collection([1, 2, 3, 4]);

        $data = $data->takeWhile(function ($item) {
            return $item < 3;
        });

        $this->assertSame([1, 2], $data->toArray());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testTakeWhileReturnsNoItemsForUnmetValue($collection) {
        $data = new $collection([1, 2, 3, 4]);

        $actual = $data->takeWhile(2);

        $this->assertSame([], $actual->toArray());

        $actual = $data->takeWhile(function ($item) {
            return $item == 99;
        });

        $this->assertSame([], $actual->toArray());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testTakeWhileCanBeProxied($collection) {
        $data = new $collection([
            new TestSupportCollectionHigherOrderItem('Adam'),
            new TestSupportCollectionHigherOrderItem('Adam'),
            new TestSupportCollectionHigherOrderItem('Taylor'),
            new TestSupportCollectionHigherOrderItem('Taylor'),
        ]);

        $actual = $data->takeWhile->is('Adam');

        $this->assertCount(2, $actual);
        $this->assertSame('Adam', $actual->get(0)->name);
        $this->assertSame('Adam', $actual->get(1)->name);
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testMacroable($collection) {
        // Foo() macro : unique values starting with A
        $collection::macro('foo', function () {
            /** @var CCollection $this */
            return $this->filter(function ($item) {
                return strpos($item, 'a') === 0;
            })
                ->unique()
                ->values();
        });

        $c = new $collection(['a', 'a', 'aa', 'aaa', 'bar']);

        $this->assertSame(['a', 'aa', 'aaa'], $c->foo()->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testCanAddMethodsToProxy($collection) {
        $collection::macro('adults', function ($callback) {
            /** @var CCollection $this */
            return $this->filter(function ($item) use ($callback) {
                return $callback($item) >= 18;
            });
        });

        $collection::proxy('adults');

        $c = new $collection([['age' => 3], ['age' => 12], ['age' => 18], ['age' => 56]]);

        $this->assertSame([['age' => 18], ['age' => 56]], $c->adults->age->values()->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testMakeMethod($collection) {
        $data = $collection::make('foo');
        $this->assertEquals(['foo'], $data->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testMakeMethodFromNull($collection) {
        $data = $collection::make(null);
        $this->assertEquals([], $data->all());

        $data = $collection::make();
        $this->assertEquals([], $data->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testMakeMethodFromCollection($collection) {
        $firstCollection = $collection::make(['foo' => 'bar']);
        $secondCollection = $collection::make($firstCollection);
        $this->assertEquals(['foo' => 'bar'], $secondCollection->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testMakeMethodFromArray($collection) {
        $data = $collection::make(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $data->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testWrapWithScalar($collection) {
        $data = $collection::wrap('foo');
        $this->assertEquals(['foo'], $data->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testWrapWithArray($collection) {
        $data = $collection::wrap(['foo']);
        $this->assertEquals(['foo'], $data->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testWrapWithArrayable($collection) {
        $data = $collection::wrap($o = new TestArrayableObject());
        $this->assertEquals([$o], $data->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testWrapWithJsonable($collection) {
        $data = $collection::wrap($o = new TestJsonableObject());
        $this->assertEquals([$o], $data->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testWrapWithJsonSerialize($collection) {
        $data = $collection::wrap($o = new TestJsonSerializeObject());
        $this->assertEquals([$o], $data->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testWrapWithCollectionClass($collection) {
        $data = $collection::wrap($collection::make(['foo']));
        $this->assertEquals(['foo'], $data->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testWrapWithCollectionSubclass($collection) {
        $data = TestCollectionSubclass::wrap($collection::make(['foo']));
        $this->assertEquals(['foo'], $data->all());
        $this->assertInstanceOf(TestCollectionSubclass::class, $data);
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testUnwrapCollection($collection) {
        $data = new $collection(['foo']);
        $this->assertEquals(['foo'], $collection::unwrap($data));
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testUnwrapCollectionWithArray($collection) {
        $this->assertEquals(['foo'], $collection::unwrap(['foo']));
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testUnwrapCollectionWithScalar($collection) {
        $this->assertSame('foo', $collection::unwrap('foo'));
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testEmptyMethod($collection) {
        $collection = $collection::createEmpty();

        $this->assertCount(0, $collection->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testTimesMethod($collection) {
        $two = $collection::times(2, function ($number) {
            return 'slug-' . $number;
        });

        $zero = $collection::times(0, function ($number) {
            return 'slug-' . $number;
        });

        $negative = $collection::times(-4, function ($number) {
            return 'slug-' . $number;
        });

        $range = $collection::times(5);

        $this->assertEquals(['slug-1', 'slug-2'], $two->all());
        $this->assertTrue($zero->isEmpty());
        $this->assertTrue($negative->isEmpty());
        $this->assertEquals(range(1, 5), $range->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testRangeMethod($collection) {
        $this->assertSame(
            [1, 2, 3, 4, 5],
            $collection::range(1, 5)->all()
        );

        $this->assertSame(
            [-2, -1, 0, 1, 2],
            $collection::range(-2, 2)->all()
        );

        $this->assertSame(
            [-4, -3, -2],
            $collection::range(-4, -2)->all()
        );

        $this->assertSame(
            [5, 4, 3, 2, 1],
            $collection::range(5, 1)->all()
        );

        $this->assertSame(
            [2, 1, 0, -1, -2],
            $collection::range(2, -2)->all()
        );

        $this->assertSame(
            [-2, -3, -4],
            $collection::range(-2, -4)->all()
        );
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testConstructMakeFromObject($collection) {
        $object = new stdClass();
        $object->foo = 'bar';
        $data = $collection::make($object);
        $this->assertEquals(['foo' => 'bar'], $data->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testConstructMethod($collection) {
        $data = new $collection('foo');
        $this->assertEquals(['foo'], $data->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testConstructMethodFromNull($collection) {
        $data = new $collection(null);
        $this->assertEquals([], $data->all());

        $data = new $collection();
        $this->assertEquals([], $data->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testConstructMethodFromCollection($collection) {
        $firstCollection = new $collection(['foo' => 'bar']);
        $secondCollection = new $collection($firstCollection);
        $this->assertEquals(['foo' => 'bar'], $secondCollection->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testConstructMethodFromArray($collection) {
        $data = new $collection(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $data->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testConstructMethodFromObject($collection) {
        $object = new stdClass();
        $object->foo = 'bar';
        $data = new $collection($object);
        $this->assertEquals(['foo' => 'bar'], $data->all());
    }

    public function testSplice() {
        $data = new CCollection(['foo', 'baz']);
        $data->splice(1);
        $this->assertEquals(['foo'], $data->all());

        $data = new CCollection(['foo', 'baz']);
        $data->splice(1, 0, 'bar');
        $this->assertEquals(['foo', 'bar', 'baz'], $data->all());

        $data = new CCollection(['foo', 'baz']);
        $data->splice(1, 1);
        $this->assertEquals(['foo'], $data->all());

        $data = new CCollection(['foo', 'baz']);
        $cut = $data->splice(1, 1, 'bar');
        $this->assertEquals(['foo', 'bar'], $data->all());
        $this->assertEquals(['baz'], $cut->all());

        $data = new CCollection(['foo', 'baz']);
        $data->splice(1, 0, ['bar']);
        $this->assertEquals(['foo', 'bar', 'baz'], $data->all());

        $data = new CCollection(['foo', 'baz']);
        $data->splice(1, 0, new CCollection(['bar']));
        $this->assertEquals(['foo', 'bar', 'baz'], $data->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testGetPluckValueWithAccessors($collection) {
        $model = new TestAccessorEloquentTestStub(['some' => 'foo']);
        $modelTwo = new TestAccessorEloquentTestStub(['some' => 'bar']);
        $data = new $collection([$model, $modelTwo]);

        $this->assertEquals(['foo', 'bar'], $data->pluck('some')->all());
    }

    public function testTransform() {
        $data = new CCollection(['first' => 'crese', 'last' => 'nity']);
        $data->transform(function ($item, $key) {
            return $key . '-' . strrev($item);
        });
        $this->assertEquals(['first' => 'first-eserc', 'last' => 'last-ytin'], $data->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testKeyByAttribute($collection) {
        $data = new $collection([['rating' => 1, 'name' => '1'], ['rating' => 2, 'name' => '2'], ['rating' => 3, 'name' => '3']]);

        $result = $data->keyBy('rating');
        $this->assertEquals([1 => ['rating' => 1, 'name' => '1'], 2 => ['rating' => 2, 'name' => '2'], 3 => ['rating' => 3, 'name' => '3']], $result->all());

        $result = $data->keyBy(function ($item) {
            return $item['rating'] * 2;
        });
        $this->assertEquals([2 => ['rating' => 1, 'name' => '1'], 4 => ['rating' => 2, 'name' => '2'], 6 => ['rating' => 3, 'name' => '3']], $result->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testKeyByClosure($collection) {
        $data = new $collection([
            ['firstname' => 'Crese', 'lastname' => 'Nity', 'locale' => 'US'],
            ['firstname' => 'Lucas', 'lastname' => 'Michot', 'locale' => 'FR'],
        ]);
        $result = $data->keyBy(function ($item, $key) {
            return strtolower($key . '-' . $item['firstname'] . $item['lastname']);
        });
        $this->assertEquals([
            '0-cresenity' => ['firstname' => 'Crese', 'lastname' => 'Nity', 'locale' => 'US'],
            '1-lucasmichot' => ['firstname' => 'Lucas', 'lastname' => 'Michot', 'locale' => 'FR'],
        ], $result->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testKeyByObject($collection) {
        $data = new $collection([
            ['firstname' => 'Taylor', 'lastname' => 'Otwell', 'locale' => 'US'],
            ['firstname' => 'Lucas', 'lastname' => 'Michot', 'locale' => 'FR'],
        ]);
        $result = $data->keyBy(function ($item, $key) use ($collection) {
            return new $collection([$key, $item['firstname'], $item['lastname']]);
        });
        $this->assertEquals([
            '[0,"Taylor","Otwell"]' => ['firstname' => 'Taylor', 'lastname' => 'Otwell', 'locale' => 'US'],
            '[1,"Lucas","Michot"]' => ['firstname' => 'Lucas', 'lastname' => 'Michot', 'locale' => 'FR'],
        ], $result->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testValueRetrieverAcceptsDotNotation($collection) {
        $c = new $collection([
            (object) ['id' => 1, 'foo' => ['bar' => 'B']], (object) ['id' => 2, 'foo' => ['bar' => 'A']],
        ]);

        $c = $c->sortBy('foo.bar');
        $this->assertEquals([2, 1], $c->pluck('id')->all());
    }

    public function testPullRetrievesItemFromCollection() {
        $c = new CCollection(['foo', 'bar']);

        $this->assertSame('foo', $c->pull(0));
    }

    public function testPullRemovesItemFromCollection() {
        $c = new CCollection(['foo', 'bar']);
        $c->pull(0);
        $this->assertEquals([1 => 'bar'], $c->all());
    }

    public function testPullReturnsDefault() {
        $c = new CCollection([]);
        $value = $c->pull(0, 'foo');
        $this->assertSame('foo', $value);
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testRejectRemovesElementsPassingTruthTest($collection) {
        $c = new $collection(['foo', 'bar']);
        $this->assertEquals(['foo'], $c->reject('bar')->values()->all());

        $c = new $collection(['foo', 'bar']);
        $this->assertEquals(['foo'], $c->reject(function ($v) {
            return $v === 'bar';
        })->values()->all());

        $c = new $collection(['foo', null]);
        $this->assertEquals(['foo'], $c->reject(null)->values()->all());

        $c = new $collection(['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $c->reject('baz')->values()->all());

        $c = new $collection(['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $c->reject(function ($v) {
            return $v === 'baz';
        })->values()->all());

        $c = new $collection(['id' => 1, 'primary' => 'foo', 'secondary' => 'bar']);
        $this->assertEquals(['primary' => 'foo', 'secondary' => 'bar'], $c->reject(function ($item, $key) {
            return $key === 'id';
        })->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testRejectWithoutAnArgumentRemovesTruthyValues($collection) {
        $data1 = new $collection([
            false,
            true,
            new $collection(),
            0,
        ]);
        $this->assertSame([0 => false, 3 => 0], $data1->reject()->all());

        $data2 = new $collection([
            'a' => true,
            'b' => true,
            'c' => true,
        ]);
        $this->assertTrue(
            $data2->reject()->isEmpty()
        );
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testSearchReturnsIndexOfFirstFoundItem($collection) {
        $c = new $collection([1, 2, 3, 4, 5, 2, 5, 'foo' => 'bar']);

        $this->assertEquals(1, $c->search(2));
        $this->assertEquals(1, $c->search('2'));
        $this->assertSame('foo', $c->search('bar'));
        $this->assertEquals(4, $c->search(function ($value) {
            return $value > 4;
        }));
        $this->assertSame('foo', $c->search(function ($value) {
            return !is_numeric($value);
        }));
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testSearchInStrictMode($collection) {
        $c = new $collection([false, 0, 1, [], '']);
        $this->assertFalse($c->search('false', true));
        $this->assertFalse($c->search('1', true));
        $this->assertEquals(0, $c->search(false, true));
        $this->assertEquals(1, $c->search(0, true));
        $this->assertEquals(2, $c->search(1, true));
        $this->assertEquals(3, $c->search([], true));
        $this->assertEquals(4, $c->search('', true));
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testSearchReturnsFalseWhenItemIsNotFound($collection) {
        $c = new $collection([1, 2, 3, 4, 5, 'foo' => 'bar']);

        $this->assertFalse($c->search(6));
        $this->assertFalse($c->search('foo'));
        $this->assertFalse($c->search(function ($value) {
            return $value < 1 && is_numeric($value);
        }));
        $this->assertFalse($c->search(function ($value) {
            return $value === 'nope';
        }));
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testKeys($collection) {
        $c = new $collection(['name' => 'ither', 'framework' => 'laravel']);
        $this->assertEquals(['name', 'framework'], $c->keys()->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testPaginate($collection) {
        $c = new $collection(['one', 'two', 'three', 'four']);
        $this->assertEquals(['one', 'two'], $c->forPage(0, 2)->all());
        $this->assertEquals(['one', 'two'], $c->forPage(1, 2)->all());
        $this->assertEquals([2 => 'three', 3 => 'four'], $c->forPage(2, 2)->all());
        $this->assertEquals([], $c->forPage(3, 2)->all());
    }

    public function testPrepend() {
        $c = new CCollection(['one', 'two', 'three', 'four']);
        $this->assertEquals(
            ['zero', 'one', 'two', 'three', 'four'],
            $c->prepend('zero')->all()
        );

        $c = new CCollection(['one' => 1, 'two' => 2]);
        $this->assertEquals(
            ['zero' => 0, 'one' => 1, 'two' => 2],
            $c->prepend(0, 'zero')->all()
        );

        $c = new CCollection(['one' => 1, 'two' => 2]);
        $this->assertEquals(
            [null => 0, 'one' => 1, 'two' => 2],
            $c->prepend(0, null)->all()
        );
    }

    public function testPushWithOneItem() {
        $expected = [
            0 => 4,
            1 => 5,
            2 => 6,
            3 => ['a', 'b', 'c'],
            4 => ['who' => 'Jonny', 'preposition' => 'from', 'where' => 'Laroe'],
            5 => 'Jonny from Laroe',
        ];

        $data = new CCollection([4, 5, 6]);
        $data->push(['a', 'b', 'c']);
        $data->push(['who' => 'Jonny', 'preposition' => 'from', 'where' => 'Laroe']);
        $actual = $data->push('Jonny from Laroe')->toArray();

        $this->assertSame($expected, $actual);
    }

    public function testPushWithMultipleItems() {
        $expected = [
            0 => 4,
            1 => 5,
            2 => 6,
            3 => 'Jonny',
            4 => 'from',
            5 => 'Laroe',
            6 => 'Jonny',
            7 => 'from',
            8 => 'Laroe',
            9 => 'a',
            10 => 'b',
            11 => 'c',
        ];

        $data = new CCollection([4, 5, 6]);
        $data->push('Jonny', 'from', 'Laroe');
        $data->push(...[11 => 'Jonny', 12 => 'from', 13 => 'Laroe']);
        $data->push(...c::collect(['a', 'b', 'c']));
        $actual = $data->push(...[])->toArray();

        $this->assertSame($expected, $actual);
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testZip($collection) {
        $c = new $collection([1, 2, 3]);
        $c = $c->zip(new $collection([4, 5, 6]));
        $this->assertInstanceOf($collection, $c);
        $this->assertInstanceOf($collection, $c->get(0));
        $this->assertInstanceOf($collection, $c->get(1));
        $this->assertInstanceOf($collection, $c->get(2));
        $this->assertCount(3, $c);
        $this->assertEquals([1, 4], $c->get(0)->all());
        $this->assertEquals([2, 5], $c->get(1)->all());
        $this->assertEquals([3, 6], $c->get(2)->all());

        $c = new $collection([1, 2, 3]);
        $c = $c->zip([4, 5, 6], [7, 8, 9]);
        $this->assertCount(3, $c);
        $this->assertEquals([1, 4, 7], $c->get(0)->all());
        $this->assertEquals([2, 5, 8], $c->get(1)->all());
        $this->assertEquals([3, 6, 9], $c->get(2)->all());

        $c = new $collection([1, 2, 3]);
        $c = $c->zip([4, 5, 6], [7]);
        $this->assertCount(3, $c);
        $this->assertEquals([1, 4, 7], $c->get(0)->all());
        $this->assertEquals([2, 5, null], $c->get(1)->all());
        $this->assertEquals([3, 6, null], $c->get(2)->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testPadPadsArrayWithValue($collection) {
        $c = new $collection([1, 2, 3]);
        $c = $c->pad(4, 0);
        $this->assertEquals([1, 2, 3, 0], $c->all());

        $c = new $collection([1, 2, 3, 4, 5]);
        $c = $c->pad(4, 0);
        $this->assertEquals([1, 2, 3, 4, 5], $c->all());

        $c = new $collection([1, 2, 3]);
        $c = $c->pad(-4, 0);
        $this->assertEquals([0, 1, 2, 3], $c->all());

        $c = new $collection([1, 2, 3, 4, 5]);
        $c = $c->pad(-4, 0);
        $this->assertEquals([1, 2, 3, 4, 5], $c->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testGettingMaxItemsFromCollection($collection) {
        $c = new $collection([(object) ['foo' => 10], (object) ['foo' => 20]]);
        $this->assertEquals(20, $c->max(function ($item) {
            return $item->foo;
        }));
        $this->assertEquals(20, $c->max('foo'));
        $this->assertEquals(20, $c->max->foo);

        $c = new $collection([['foo' => 10], ['foo' => 20]]);
        $this->assertEquals(20, $c->max('foo'));
        $this->assertEquals(20, $c->max->foo);

        $c = new $collection([1, 2, 3, 4, 5]);
        $this->assertEquals(5, $c->max());

        $c = new $collection();
        $this->assertNull($c->max());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testGettingMinItemsFromCollection($collection) {
        $c = new $collection([(object) ['foo' => 10], (object) ['foo' => 20]]);
        $this->assertEquals(10, $c->min(function ($item) {
            return $item->foo;
        }));
        $this->assertEquals(10, $c->min('foo'));
        $this->assertEquals(10, $c->min->foo);

        $c = new $collection([['foo' => 10], ['foo' => 20]]);
        $this->assertEquals(10, $c->min('foo'));
        $this->assertEquals(10, $c->min->foo);

        $c = new $collection([['foo' => 10], ['foo' => 20], ['foo' => null]]);
        $this->assertEquals(10, $c->min('foo'));
        $this->assertEquals(10, $c->min->foo);

        $c = new $collection([1, 2, 3, 4, 5]);
        $this->assertEquals(1, $c->min());

        $c = new $collection([1, null, 3, 4, 5]);
        $this->assertEquals(1, $c->min());

        $c = new $collection([0, 1, 2, 3, 4]);
        $this->assertEquals(0, $c->min());

        $c = new $collection();
        $this->assertNull($c->min());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testOnly($collection) {
        $data = new $collection(['first' => 'Taylor', 'last' => 'Otwell', 'email' => 'cresenity@gmail.com']);

        $this->assertEquals($data->all(), $data->only(null)->all());
        $this->assertEquals(['first' => 'Taylor'], $data->only(['first', 'missing'])->all());
        $this->assertEquals(['first' => 'Taylor'], $data->only('first', 'missing')->all());
        $this->assertEquals(['first' => 'Taylor'], $data->only(c::collect(['first', 'missing']))->all());

        $this->assertEquals(['first' => 'Taylor', 'email' => 'cresenity@gmail.com'], $data->only(['first', 'email'])->all());
        $this->assertEquals(['first' => 'Taylor', 'email' => 'cresenity@gmail.com'], $data->only('first', 'email')->all());
        $this->assertEquals(['first' => 'Taylor', 'email' => 'cresenity@gmail.com'], $data->only(c::collect(['first', 'email']))->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testCombineWithArray($collection) {
        $expected = [
            1 => 4,
            2 => 5,
            3 => 6,
        ];

        $c = new $collection(array_keys($expected));
        $actual = $c->combine(array_values($expected))->toArray();

        $this->assertSame($expected, $actual);
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testCombineWithCollection($collection) {
        $expected = [
            1 => 4,
            2 => 5,
            3 => 6,
        ];

        $keyCollection = new $collection(array_keys($expected));
        $valueCollection = new $collection(array_values($expected));
        $actual = $keyCollection->combine($valueCollection)->toArray();

        $this->assertSame($expected, $actual);
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testConcatWithArray($collection) {
        $expected = [
            0 => 4,
            1 => 5,
            2 => 6,
            3 => 'a',
            4 => 'b',
            5 => 'c',
            6 => 'Jonny',
            7 => 'from',
            8 => 'Laroe',
            9 => 'Jonny',
            10 => 'from',
            11 => 'Laroe',
        ];

        $data = new $collection([4, 5, 6]);
        $data = $data->concat(['a', 'b', 'c']);
        $data = $data->concat(['who' => 'Jonny', 'preposition' => 'from', 'where' => 'Laroe']);
        $actual = $data->concat(['who' => 'Jonny', 'preposition' => 'from', 'where' => 'Laroe'])->toArray();

        $this->assertSame($expected, $actual);
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testConcatWithCollection($collection) {
        $expected = [
            0 => 4,
            1 => 5,
            2 => 6,
            3 => 'a',
            4 => 'b',
            5 => 'c',
            6 => 'Jonny',
            7 => 'from',
            8 => 'Laroe',
            9 => 'Jonny',
            10 => 'from',
            11 => 'Laroe',
        ];

        $firstCollection = new $collection([4, 5, 6]);
        $secondCollection = new $collection(['a', 'b', 'c']);
        $thirdCollection = new $collection(['who' => 'Jonny', 'preposition' => 'from', 'where' => 'Laroe']);
        $firstCollection = $firstCollection->concat($secondCollection);
        $firstCollection = $firstCollection->concat($thirdCollection);
        $actual = $firstCollection->concat($thirdCollection)->toArray();

        $this->assertSame($expected, $actual);
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testDump($collection) {
        $log = new CCollection();

        VarDumper::setHandler(function ($value) use ($log) {
            $log->add($value);
        });

        (new $collection([1, 2, 3]))->dump('one', 'two');

        $this->assertSame(['one', 'two', [1, 2, 3]], $log->all());

        VarDumper::setHandler(null);
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testReduce($collection) {
        $data = new $collection([1, 2, 3]);
        $this->assertEquals(6, $data->reduce(function ($carry, $element) {
            return $carry += $element;
        }));

        $data = new $collection([
            'foo' => 'bar',
            'baz' => 'qux',
        ]);
        $this->assertSame('foobarbazqux', $data->reduce(function ($carry, $element, $key) {
            return $carry .= $key . $element;
        }));
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testReduceWithKeys($collection) {
        $data = new $collection([
            'foo' => 'bar',
            'baz' => 'qux',
        ]);
        $this->assertSame('foobarbazqux', $data->reduceWithKeys(function ($carry, $element, $key) {
            return $carry .= $key . $element;
        }));
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testReduceSpread($collection) {
        $data = new $collection([-1, 0, 1, 2, 3, 4, 5]);

        list($sum, $max, $min) = $data->reduceSpread(function ($sum, $max, $min, $value) {
            $sum += $value;
            $max = max($max, $value);
            $min = min($min, $value);

            return [$sum, $max, $min];
        }, 0, PHP_INT_MIN, PHP_INT_MAX);

        $this->assertEquals(14, $sum);
        $this->assertEquals(5, $max);
        $this->assertEquals(-1, $min);
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testPipe($collection) {
        $data = new $collection([1, 2, 3]);

        $this->assertEquals(6, $data->pipe(function ($data) {
            return $data->sum();
        }));
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testPipeInto($collection) {
        $data = new $collection([
            'first', 'second',
        ]);

        $instance = $data->pipeInto(TestCollectionMapIntoObject::class);

        $this->assertSame($data, $instance->value);
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testMedianValueWithArrayCollection($collection) {
        $data = new $collection([1, 2, 2, 4]);

        $this->assertEquals(2, $data->median());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testMedianValueByKey($collection) {
        $data = new $collection([
            (object) ['foo' => 1],
            (object) ['foo' => 2],
            (object) ['foo' => 2],
            (object) ['foo' => 4],
        ]);
        $this->assertEquals(2, $data->median('foo'));
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testMedianOnCollectionWithNull($collection) {
        $data = new $collection([
            (object) ['foo' => 1],
            (object) ['foo' => 2],
            (object) ['foo' => 4],
            (object) ['foo' => null],
        ]);
        $this->assertEquals(2, $data->median('foo'));
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testEvenMedianCollection($collection) {
        $data = new $collection([
            (object) ['foo' => 0],
            (object) ['foo' => 3],
        ]);
        $this->assertEquals(1.5, $data->median('foo'));
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testMedianOutOfOrderCollection($collection) {
        $data = new $collection([
            (object) ['foo' => 0],
            (object) ['foo' => 5],
            (object) ['foo' => 3],
        ]);
        $this->assertEquals(3, $data->median('foo'));
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testMedianOnEmptyCollectionReturnsNull($collection) {
        $data = new $collection();
        $this->assertNull($data->median());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testModeOnNullCollection($collection) {
        $data = new $collection();
        $this->assertNull($data->mode());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testMode($collection) {
        $data = new $collection([1, 2, 3, 4, 4, 5]);
        $this->assertEquals([4], $data->mode());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testModeValueByKey($collection) {
        $data = new $collection([
            (object) ['foo' => 1],
            (object) ['foo' => 1],
            (object) ['foo' => 2],
            (object) ['foo' => 4],
        ]);
        $this->assertEquals([1], $data->mode('foo'));
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testWithMultipleModeValues($collection) {
        $data = new $collection([1, 2, 2, 1]);
        $this->assertEquals([1, 2], $data->mode());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testHasReturnsValidResults($collection) {
        $data = new $collection(['foo' => 'one', 'bar' => 'two', 1 => 'three']);
        $this->assertTrue($data->has('foo'));
        $this->assertTrue($data->has('foo', 'bar', 1));
        $this->assertFalse($data->has('foo', 'bar', 1, 'baz'));
        $this->assertFalse($data->has('baz'));
    }

    public function testPutAddsItemToCollection() {
        $data = new CCollection();
        $this->assertSame([], $data->toArray());
        $data->put('foo', 1);
        $this->assertSame(['foo' => 1], $data->toArray());
        $data->put('bar', ['nested' => 'two']);
        $this->assertSame(['foo' => 1, 'bar' => ['nested' => 'two']], $data->toArray());
        $data->put('foo', 3);
        $this->assertSame(['foo' => 3, 'bar' => ['nested' => 'two']], $data->toArray());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testGetWithNullReturnsNull($collection) {
        $data = new $collection([1, 2, 3]);
        $this->assertNull($data->get(null));
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testCollect($collection) {
        $data = $collection::make([
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ])->collect();

        $this->assertInstanceOf(CCollection::class, $data);

        $this->assertSame([
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ], $data->all());
    }

    /**
     * @param CCollection $collection
     *
     * @dataProvider collectionClassProvider
     */
    public function testUndot($collection) {
        $data = $collection::make([
            'name' => 'Taylor',
            'meta.foo' => 'bar',
            'meta.baz' => 'boom',
            'meta.bam.boom' => 'bip',
        ])->undot();
        $this->assertSame([
            'name' => 'Taylor',
            'meta' => [
                'foo' => 'bar',
                'baz' => 'boom',
                'bam' => [
                    'boom' => 'bip',
                ],
            ],
        ], $data->all());

        $data = $collection::make([
            'foo.0' => 'bar',
            'foo.1' => 'baz',
            'foo.baz' => 'boom',
        ])->undot();
        $this->assertSame([
            'foo' => [
                'bar',
                'baz',
                'baz' => 'boom',
            ],
        ], $data->all());
    }

    /**
     * Provides each collection class, respectively.
     *
     * @return array
     */
    public function collectionClassProvider() {
        return [
            [CCollection::class],
            //[LazyCollection::class],
        ];
    }
}
// @codingStandardsIgnoreStart
class TestSupportCollectionHigherOrderItem {
    public $name;

    public function __construct($name = 'ither') {
        $this->name = $name;
    }

    public function uppercase() {
        return $this->name = strtoupper($this->name);
    }

    public function is($name) {
        return $this->name === $name;
    }
}

class TestAccessorEloquentTestStub {
    protected $attributes = [];

    public function __construct($attributes) {
        $this->attributes = $attributes;
    }

    public function __get($attribute) {
        $accessor = 'get' . lcfirst($attribute) . 'Attribute';
        if (method_exists($this, $accessor)) {
            return $this->$accessor();
        }

        return $this->$attribute;
    }

    public function __isset($attribute) {
        $accessor = 'get' . lcfirst($attribute) . 'Attribute';

        if (method_exists($this, $accessor)) {
            return !is_null($this->$accessor());
        }

        return isset($this->$attribute);
    }

    public function getSomeAttribute() {
        return $this->attributes['some'];
    }
}

class TestArrayAccessImplementation implements ArrayAccess {
    private $arr;

    public function __construct($arr) {
        $this->arr = $arr;
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset) {
        return isset($this->arr[$offset]);
    }

    public function offsetGet($offset) {
        return $this->arr[$offset];
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value) {
        $this->arr[$offset] = $value;
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset) {
        unset($this->arr[$offset]);
    }
}

class TestArrayableObject implements Arrayable {
    public function toArray() {
        return ['foo' => 'bar'];
    }
}

class TestJsonableObject implements Jsonable {
    public function toJson($options = 0) {
        return '{"foo":"bar"}';
    }
}

class TestJsonSerializeObject implements JsonSerializable {
    public function jsonSerialize(): array {
        return ['foo' => 'bar'];
    }
}

class TestJsonSerializeWithScalarValueObject implements JsonSerializable {
    public function jsonSerialize(): string {
        return 'foo';
    }
}

class TestCollectionMapIntoObject {
    public $value;

    public function __construct($value) {
        $this->value = $value;
    }
}

class TestCollectionSubclass extends CCollection {
}
