<?php

trait CollectionTest_PartitionTrait {
    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSliceOffset($collection) {
        $data = new $collection([1, 2, 3, 4, 5, 6, 7, 8]);
        $this->assertEquals([4, 5, 6, 7, 8], $data->slice(3)->values()->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSliceNegativeOffset($collection) {
        $data = new $collection([1, 2, 3, 4, 5, 6, 7, 8]);
        $this->assertEquals([6, 7, 8], $data->slice(-3)->values()->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSliceOffsetAndLength($collection) {
        $data = new $collection([1, 2, 3, 4, 5, 6, 7, 8]);
        $this->assertEquals([4, 5, 6], $data->slice(3, 3)->values()->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSliceOffsetAndNegativeLength($collection) {
        $data = new $collection([1, 2, 3, 4, 5, 6, 7, 8]);
        $this->assertEquals([4, 5, 6, 7], $data->slice(3, -1)->values()->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSliceNegativeOffsetAndLength($collection) {
        $data = new $collection([1, 2, 3, 4, 5, 6, 7, 8]);
        $this->assertEquals([4, 5, 6], $data->slice(-5, 3)->values()->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSliceNegativeOffsetAndNegativeLength($collection) {
        $data = new $collection([1, 2, 3, 4, 5, 6, 7, 8]);
        $this->assertEquals([3, 4, 5, 6], $data->slice(-6, -2)->values()->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testCollectionFromTraversable($collection) {
        $data = new $collection(new ArrayObject([1, 2, 3]));
        $this->assertEquals([1, 2, 3], $data->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testCollectionFromTraversableWithKeys($collection) {
        $data = new $collection(new ArrayObject(['foo' => 1, 'bar' => 2, 'baz' => 3]));
        $this->assertEquals(['foo' => 1, 'bar' => 2, 'baz' => 3], $data->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSplitCollectionWithADivisableCount($collection) {
        $data = new $collection(['a', 'b', 'c', 'd']);

        $this->assertEquals(
            [['a', 'b'], ['c', 'd']],
            $data->split(2)->map(function (CCollection $chunk) {
                return $chunk->values()->toArray();
            })->toArray()
        );

        $data = new $collection([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        $this->assertEquals(
            [[1, 2, 3, 4, 5], [6, 7, 8, 9, 10]],
            $data->split(2)->map(function (CCollection $chunk) {
                return $chunk->values()->toArray();
            })->toArray()
        );
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSplitCollectionWithAnUndivisableCount($collection) {
        $data = new $collection(['a', 'b', 'c']);

        $this->assertEquals(
            [['a', 'b'], ['c']],
            $data->split(2)->map(function (CCollection $chunk) {
                return $chunk->values()->toArray();
            })->toArray()
        );
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSplitCollectionWithCountLessThenDivisor($collection) {
        $data = new $collection(['a']);

        $this->assertEquals(
            [['a']],
            $data->split(2)->map(function (CCollection $chunk) {
                return $chunk->values()->toArray();
            })->toArray()
        );
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSplitCollectionIntoThreeWithCountOfFour($collection) {
        $data = new $collection(['a', 'b', 'c', 'd']);

        $this->assertEquals(
            [['a', 'b'], ['c'], ['d']],
            $data->split(3)->map(function (CCollection $chunk) {
                return $chunk->values()->toArray();
            })->toArray()
        );
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSplitCollectionIntoThreeWithCountOfFive($collection) {
        $data = new $collection(['a', 'b', 'c', 'd', 'e']);

        $this->assertEquals(
            [['a', 'b'], ['c', 'd'], ['e']],
            $data->split(3)->map(function (CCollection $chunk) {
                return $chunk->values()->toArray();
            })->toArray()
        );
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSplitCollectionIntoSixWithCountOfTen($collection) {
        $data = new $collection(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j']);

        $this->assertEquals(
            [['a', 'b'], ['c', 'd'], ['e', 'f'], ['g', 'h'], ['i'], ['j']],
            $data->split(6)->map(function (CCollection $chunk) {
                return $chunk->values()->toArray();
            })->toArray()
        );
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSplitEmptyCollection($collection) {
        $data = new $collection();

        $this->assertEquals(
            [],
            $data->split(2)->map(function (CCollection $chunk) {
                return $chunk->values()->toArray();
            })->toArray()
        );
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testPartition($collection) {
        $data = new $collection(range(1, 10));

        list($firstPartition, $secondPartition) = $data->partition(function ($i) {
            return $i <= 5;
        })->all();

        $this->assertEquals([1, 2, 3, 4, 5], $firstPartition->values()->toArray());
        $this->assertEquals([6, 7, 8, 9, 10], $secondPartition->values()->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testPartitionCallbackWithKey($collection) {
        $data = new $collection(['zero', 'one', 'two', 'three']);

        list($even, $odd) = $data->partition(function ($item, $index) {
            return $index % 2 === 0;
        })->all();

        $this->assertEquals(['zero', 'two'], $even->values()->toArray());
        $this->assertEquals(['one', 'three'], $odd->values()->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testPartitionByKey($collection) {
        $courses = new $collection([
            ['free' => true, 'title' => 'Basic'], ['free' => false, 'title' => 'Premium'],
        ]);

        list($free, $premium) = $courses->partition('free')->all();

        $this->assertSame([['free' => true, 'title' => 'Basic']], $free->values()->toArray());
        $this->assertSame([['free' => false, 'title' => 'Premium']], $premium->values()->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testPartitionWithOperators($collection) {
        $data = new $collection([
            ['name' => 'Tim', 'age' => 17],
            ['name' => 'Agatha', 'age' => 62],
            ['name' => 'Kristina', 'age' => 33],
            ['name' => 'Tim', 'age' => 41],
        ]);

        list($tims, $others) = $data->partition('name', 'Tim')->all();

        $this->assertEquals([
            ['name' => 'Tim', 'age' => 17],
            ['name' => 'Tim', 'age' => 41],
        ], $tims->values()->all());

        $this->assertEquals([
            ['name' => 'Agatha', 'age' => 62],
            ['name' => 'Kristina', 'age' => 33],
        ], $others->values()->all());

        list($adults, $minors) = $data->partition('age', '>=', 18)->all();

        $this->assertEquals([
            ['name' => 'Agatha', 'age' => 62],
            ['name' => 'Kristina', 'age' => 33],
            ['name' => 'Tim', 'age' => 41],
        ], $adults->values()->all());

        $this->assertEquals([
            ['name' => 'Tim', 'age' => 17],
        ], $minors->values()->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testPartitionPreservesKeys($collection) {
        $courses = new $collection([
            'a' => ['free' => true], 'b' => ['free' => false], 'c' => ['free' => true],
        ]);

        list($free, $premium) = $courses->partition('free')->all();

        $this->assertSame(['a' => ['free' => true], 'c' => ['free' => true]], $free->toArray());
        $this->assertSame(['b' => ['free' => false]], $premium->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testPartitionEmptyCollection($collection) {
        $data = new $collection();

        $this->assertCount(2, $data->partition(function () {
            return true;
        }));
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testHigherOrderPartition($collection) {
        $courses = new $collection([
            'a' => ['free' => true], 'b' => ['free' => false], 'c' => ['free' => true],
        ]);

        list($free, $premium) = $courses->partition->free->all();

        $this->assertSame(['a' => ['free' => true], 'c' => ['free' => true]], $free->toArray());

        $this->assertSame(['b' => ['free' => false]], $premium->toArray());
    }
}
