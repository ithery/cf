<?php
trait CollectionTest_OperationTrait {
    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testFlatten($collection) {
        // Flat arrays are unaffected
        $c = new $collection(['#foo', '#bar', '#baz']);
        $this->assertEquals(['#foo', '#bar', '#baz'], $c->flatten()->all());

        // Nested arrays are flattened with existing flat items
        $c = new $collection([['#foo', '#bar'], '#baz']);
        $this->assertEquals(['#foo', '#bar', '#baz'], $c->flatten()->all());

        // Sets of nested arrays are flattened
        $c = new $collection([['#foo', '#bar'], ['#baz']]);
        $this->assertEquals(['#foo', '#bar', '#baz'], $c->flatten()->all());

        // Deeply nested arrays are flattened
        $c = new $collection([['#foo', ['#bar']], ['#baz']]);
        $this->assertEquals(['#foo', '#bar', '#baz'], $c->flatten()->all());

        // Nested collections are flattened alongside arrays
        $c = new $collection([new $collection(['#foo', '#bar']), ['#baz']]);
        $this->assertEquals(['#foo', '#bar', '#baz'], $c->flatten()->all());

        // Nested collections containing plain arrays are flattened
        $c = new $collection([new $collection(['#foo', ['#bar']]), ['#baz']]);
        $this->assertEquals(['#foo', '#bar', '#baz'], $c->flatten()->all());

        // Nested arrays containing collections are flattened
        $c = new $collection([['#foo', new $collection(['#bar'])], ['#baz']]);
        $this->assertEquals(['#foo', '#bar', '#baz'], $c->flatten()->all());

        // Nested arrays containing collections containing arrays are flattened
        $c = new $collection([['#foo', new $collection(['#bar', ['#zap']])], ['#baz']]);
        $this->assertEquals(['#foo', '#bar', '#zap', '#baz'], $c->flatten()->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testFlattenWithDepth($collection) {
        // No depth flattens recursively
        $c = new $collection([['#foo', ['#bar', ['#baz']]], '#zap']);
        $this->assertEquals(['#foo', '#bar', '#baz', '#zap'], $c->flatten()->all());

        // Specifying a depth only flattens to that depth
        $c = new $collection([['#foo', ['#bar', ['#baz']]], '#zap']);
        $this->assertEquals(['#foo', ['#bar', ['#baz']], '#zap'], $c->flatten(1)->all());

        $c = new $collection([['#foo', ['#bar', ['#baz']]], '#zap']);
        $this->assertEquals(['#foo', '#bar', ['#baz'], '#zap'], $c->flatten(2)->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testFlattenIgnoresKeys($collection) {
        // No depth ignores keys
        $c = new $collection(['#foo', ['key' => '#bar'], ['key' => '#baz'], 'key' => '#zap']);
        $this->assertEquals(['#foo', '#bar', '#baz', '#zap'], $c->flatten()->all());

        // Depth of 1 ignores keys
        $c = new $collection(['#foo', ['key' => '#bar'], ['key' => '#baz'], 'key' => '#zap']);
        $this->assertEquals(['#foo', '#bar', '#baz', '#zap'], $c->flatten(1)->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testMergeNull($collection) {
        $c = new $collection(['name' => 'Hello']);
        $this->assertEquals(['name' => 'Hello'], $c->merge(null)->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testMergeArray($collection) {
        $c = new $collection(['name' => 'Hello']);
        $this->assertEquals(['name' => 'Hello', 'id' => 1], $c->merge(['id' => 1])->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testMergeCollection($collection) {
        $c = new $collection(['name' => 'Hello']);
        $this->assertEquals(['name' => 'World', 'id' => 1], $c->merge(new $collection(['name' => 'World', 'id' => 1]))->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testMergeRecursiveNull($collection) {
        $c = new $collection(['name' => 'Hello']);
        $this->assertEquals(['name' => 'Hello'], $c->mergeRecursive(null)->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testMergeRecursiveArray($collection) {
        $c = new $collection(['name' => 'Hello', 'id' => 1]);
        $this->assertEquals(['name' => 'Hello', 'id' => [1, 2]], $c->mergeRecursive(['id' => 2])->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testMergeRecursiveCollection($collection) {
        $c = new $collection(['name' => 'Hello', 'id' => 1, 'meta' => ['tags' => ['a', 'b'], 'roles' => 'admin']]);
        $this->assertEquals(
            ['name' => 'Hello', 'id' => 1, 'meta' => ['tags' => ['a', 'b', 'c'], 'roles' => ['admin', 'editor']]],
            $c->mergeRecursive(new $collection(['meta' => ['tags' => ['c'], 'roles' => 'editor']]))->all()
        );
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testReplaceNull($collection) {
        $c = new $collection(['a', 'b', 'c']);
        $this->assertEquals(['a', 'b', 'c'], $c->replace(null)->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testReplaceArray($collection) {
        $c = new $collection(['a', 'b', 'c']);
        $this->assertEquals(['a', 'd', 'e'], $c->replace([1 => 'd', 2 => 'e'])->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testReplaceCollection($collection) {
        $c = new $collection(['a', 'b', 'c']);
        $this->assertEquals(
            ['a', 'd', 'e'],
            $c->replace(new $collection([1 => 'd', 2 => 'e']))->all()
        );
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testReplaceRecursiveNull($collection) {
        $c = new $collection(['a', 'b', ['c', 'd']]);
        $this->assertEquals(['a', 'b', ['c', 'd']], $c->replaceRecursive(null)->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testReplaceRecursiveArray($collection) {
        $c = new $collection(['a', 'b', ['c', 'd']]);
        $this->assertEquals(['z', 'b', ['c', 'e']], $c->replaceRecursive(['z', 2 => [1 => 'e']])->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testReplaceRecursiveCollection($collection) {
        $c = new $collection(['a', 'b', ['c', 'd']]);
        $this->assertEquals(
            ['z', 'b', ['c', 'e']],
            $c->replaceRecursive(new $collection(['z', 2 => [1 => 'e']]))->all()
        );
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testUnionNull($collection) {
        $c = new $collection(['name' => 'Hello']);
        $this->assertEquals(['name' => 'Hello'], $c->union(null)->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testUnionArray($collection) {
        $c = new $collection(['name' => 'Hello']);
        $this->assertEquals(['name' => 'Hello', 'id' => 1], $c->union(['id' => 1])->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testUnionCollection($collection) {
        $c = new $collection(['name' => 'Hello']);
        $this->assertEquals(['name' => 'Hello', 'id' => 1], $c->union(new $collection(['name' => 'World', 'id' => 1]))->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testDiffCollection($collection) {
        $c = new $collection(['id' => 1, 'first_word' => 'Hello']);
        $this->assertEquals(['id' => 1], $c->diff(new $collection(['first_word' => 'Hello', 'last_word' => 'World']))->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testDiffUsingWithCollection($collection) {
        $c = new $collection(['en_GB', 'fr', 'HR']);
        // demonstrate that diffKeys wont support case insensitivity
        $this->assertEquals(['en_GB', 'fr', 'HR'], $c->diff(new $collection(['en_gb', 'hr']))->values()->toArray());
        // allow for case insensitive difference
        $this->assertEquals(['fr'], $c->diffUsing(new $collection(['en_gb', 'hr']), 'strcasecmp')->values()->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testDiffUsingWithNull($collection) {
        $c = new $collection(['en_GB', 'fr', 'HR']);
        $this->assertEquals(['en_GB', 'fr', 'HR'], $c->diffUsing(null, 'strcasecmp')->values()->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testDiffNull($collection) {
        $c = new $collection(['id' => 1, 'first_word' => 'Hello']);
        $this->assertEquals(['id' => 1, 'first_word' => 'Hello'], $c->diff(null)->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testDiffKeys($collection) {
        $c1 = new $collection(['id' => 1, 'first_word' => 'Hello']);
        $c2 = new $collection(['id' => 123, 'foo_bar' => 'Hello']);
        $this->assertEquals(['first_word' => 'Hello'], $c1->diffKeys($c2)->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testDiffKeysUsing($collection) {
        $c1 = new $collection(['id' => 1, 'first_word' => 'Hello']);
        $c2 = new $collection(['ID' => 123, 'foo_bar' => 'Hello']);
        // demonstrate that diffKeys wont support case insensitivity
        $this->assertEquals(['id' => 1, 'first_word' => 'Hello'], $c1->diffKeys($c2)->all());
        // allow for case insensitive difference
        $this->assertEquals(['first_word' => 'Hello'], $c1->diffKeysUsing($c2, 'strcasecmp')->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testDiffAssoc($collection) {
        $c1 = new $collection(['id' => 1, 'first_word' => 'Hello', 'not_affected' => 'value']);
        $c2 = new $collection(['id' => 123, 'foo_bar' => 'Hello', 'not_affected' => 'value']);
        $this->assertEquals(['id' => 1, 'first_word' => 'Hello'], $c1->diffAssoc($c2)->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testDiffAssocUsing($collection) {
        $c1 = new $collection(['a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red']);
        $c2 = new $collection(['A' => 'green', 'yellow', 'red']);
        // demonstrate that the case of the keys will affect the output when diffAssoc is used
        $this->assertEquals(['a' => 'green', 'b' => 'brown', 'c' => 'blue', 'red'], $c1->diffAssoc($c2)->all());
        // allow for case insensitive difference
        $this->assertEquals(['b' => 'brown', 'c' => 'blue', 'red'], $c1->diffAssocUsing($c2, 'strcasecmp')->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testDuplicates($collection) {
        $duplicates = $collection::make([1, 2, 1, 'laravel', null, 'laravel', 'php', null])->duplicates()->all();
        $this->assertSame([2 => 1, 5 => 'laravel', 7 => null], $duplicates);

        // does loose comparison
        $duplicates = $collection::make([2, '2', [], null])->duplicates()->all();
        $this->assertSame([1 => '2', 3 => null], $duplicates);

        // works with mix of primitives
        $duplicates = $collection::make([1, '2', ['laravel'], ['laravel'], null, '2'])->duplicates()->all();
        $this->assertSame([3 => ['laravel'], 5 => '2'], $duplicates);

        // works with mix of objects and primitives **excepts numbers**.
        $expected = new CCollection(['laravel']);
        $duplicates = $collection::make([new CCollection(['laravel']), $expected, $expected, [], '2', '2'])->duplicates()->all();
        $this->assertSame([1 => $expected, 2 => $expected, 5 => '2'], $duplicates);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testDuplicatesWithKey($collection) {
        $items = [['framework' => 'vue'], ['framework' => 'laravel'], ['framework' => 'laravel']];
        $duplicates = $collection::make($items)->duplicates('framework')->all();
        $this->assertSame([2 => 'laravel'], $duplicates);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testDuplicatesWithCallback($collection) {
        $items = [['framework' => 'vue'], ['framework' => 'laravel'], ['framework' => 'laravel']];
        $duplicates = $collection::make($items)->duplicates(function ($item) {
            return $item['framework'];
        })->all();
        $this->assertSame([2 => 'laravel'], $duplicates);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testDuplicatesWithStrict($collection) {
        $duplicates = $collection::make([1, 2, 1, 'laravel', null, 'laravel', 'php', null])->duplicatesStrict()->all();
        $this->assertSame([2 => 1, 5 => 'laravel', 7 => null], $duplicates);

        // does strict comparison
        $duplicates = $collection::make([2, '2', [], null])->duplicatesStrict()->all();
        $this->assertSame([], $duplicates);

        // works with mix of primitives
        $duplicates = $collection::make([1, '2', ['laravel'], ['laravel'], null, '2'])->duplicatesStrict()->all();
        $this->assertSame([3 => ['laravel'], 5 => '2'], $duplicates);

        // works with mix of primitives, objects, and numbers
        $expected = new $collection(['laravel']);
        $duplicates = $collection::make([new $collection(['laravel']), $expected, $expected, [], '2', '2'])->duplicatesStrict()->all();
        $this->assertSame([2 => $expected, 5 => '2'], $duplicates);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testEach($collection) {
        $c = new $collection($original = [1, 2, 'foo' => 'bar', 'bam' => 'baz']);

        $result = [];
        $c->each(function ($item, $key) use (&$result) {
            $result[$key] = $item;
        });
        $this->assertEquals($original, $result);

        $result = [];
        $c->each(function ($item, $key) use (&$result) {
            $result[$key] = $item;
            if (is_string($key)) {
                return false;
            }
        });
        $this->assertEquals([1, 2, 'foo' => 'bar'], $result);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testEachSpread($collection) {
        $c = new $collection([[1, 'a'], [2, 'b']]);

        $result = [];
        $c->eachSpread(function ($number, $character) use (&$result) {
            $result[] = [$number, $character];
        });
        $this->assertEquals($c->all(), $result);

        $result = [];
        $c->eachSpread(function ($number, $character) use (&$result) {
            $result[] = [$number, $character];

            return false;
        });
        $this->assertEquals([[1, 'a']], $result);

        $result = [];
        $c->eachSpread(function ($number, $character, $key) use (&$result) {
            $result[] = [$number, $character, $key];
        });
        $this->assertEquals([[1, 'a', 0], [2, 'b', 1]], $result);

        $c = new $collection([new CCollection([1, 'a']), new CCollection([2, 'b'])]);
        $result = [];
        $c->eachSpread(function ($number, $character, $key) use (&$result) {
            $result[] = [$number, $character, $key];
        });
        $this->assertEquals([[1, 'a', 0], [2, 'b', 1]], $result);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testIntersectNull($collection) {
        $c = new $collection(['id' => 1, 'first_word' => 'Hello']);
        $this->assertEquals([], $c->intersect(null)->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testIntersectCollection($collection) {
        $c = new $collection(['id' => 1, 'first_word' => 'Hello']);
        $this->assertEquals(['first_word' => 'Hello'], $c->intersect(new $collection(['first_world' => 'Hello', 'last_word' => 'World']))->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testIntersectByKeysNull($collection) {
        $c = new $collection(['name' => 'Mateus', 'age' => 18]);
        $this->assertEquals([], $c->intersectByKeys(null)->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testIntersectByKeys($collection) {
        $c = new $collection(['name' => 'Mateus', 'age' => 18]);
        $this->assertEquals(['name' => 'Mateus'], $c->intersectByKeys(new $collection(['name' => 'Mateus', 'surname' => 'Guimaraes']))->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testUnique($collection) {
        $c = new $collection(['Hello', 'World', 'World']);
        $this->assertEquals(['Hello', 'World'], $c->unique()->all());

        $c = new $collection([[1, 2], [1, 2], [2, 3], [3, 4], [2, 3]]);
        $this->assertEquals([[1, 2], [2, 3], [3, 4]], $c->unique()->values()->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testUniqueWithCallback($collection) {
        $c = new $collection([
            1 => ['id' => 1, 'first' => 'Taylor', 'last' => 'Otwell'],
            2 => ['id' => 2, 'first' => 'Taylor', 'last' => 'Otwell'],
            3 => ['id' => 3, 'first' => 'Abigail', 'last' => 'Otwell'],
            4 => ['id' => 4, 'first' => 'Abigail', 'last' => 'Otwell'],
            5 => ['id' => 5, 'first' => 'Taylor', 'last' => 'Swift'],
            6 => ['id' => 6, 'first' => 'Taylor', 'last' => 'Swift'],
        ]);

        $this->assertEquals([
            1 => ['id' => 1, 'first' => 'Taylor', 'last' => 'Otwell'],
            3 => ['id' => 3, 'first' => 'Abigail', 'last' => 'Otwell'],
        ], $c->unique('first')->all());

        $this->assertEquals([
            1 => ['id' => 1, 'first' => 'Taylor', 'last' => 'Otwell'],
            3 => ['id' => 3, 'first' => 'Abigail', 'last' => 'Otwell'],
            5 => ['id' => 5, 'first' => 'Taylor', 'last' => 'Swift'],
        ], $c->unique(function ($item) {
            return $item['first'] . $item['last'];
        })->all());

        $this->assertEquals([
            1 => ['id' => 1, 'first' => 'Taylor', 'last' => 'Otwell'],
            2 => ['id' => 2, 'first' => 'Taylor', 'last' => 'Otwell'],
        ], $c->unique(function ($item, $key) {
            return $key % 2;
        })->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testUniqueStrict($collection) {
        $c = new $collection([
            [
                'id' => '0',
                'name' => 'zero',
            ],
            [
                'id' => '00',
                'name' => 'double zero',
            ],
            [
                'id' => '0',
                'name' => 'again zero',
            ],
        ]);

        $this->assertEquals([
            [
                'id' => '0',
                'name' => 'zero',
            ],
            [
                'id' => '00',
                'name' => 'double zero',
            ],
        ], $c->uniqueStrict('id')->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testCollapse($collection) {
        $data = new $collection([[$object1 = new stdClass()], [$object2 = new stdClass()]]);
        $this->assertEquals([$object1, $object2], $data->collapse()->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testCollapseWithNestedCollections($collection) {
        $data = new $collection([new $collection([1, 2, 3]), new $collection([4, 5, 6])]);
        $this->assertEquals([1, 2, 3, 4, 5, 6], $data->collapse()->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testJoin($collection) {
        $this->assertSame('a, b, c', (new $collection(['a', 'b', 'c']))->join(', '));

        $this->assertSame('a, b and c', (new $collection(['a', 'b', 'c']))->join(', ', ' and '));

        $this->assertSame('a and b', (new $collection(['a', 'b']))->join(', ', ' and '));

        $this->assertSame('a', (new $collection(['a']))->join(', ', ' and '));

        $this->assertSame('', (new $collection([]))->join(', ', ' and '));
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testCrossJoin($collection) {
        // Cross join with an array
        $this->assertEquals(
            [[1, 'a'], [1, 'b'], [2, 'a'], [2, 'b']],
            (new $collection([1, 2]))->crossJoin(['a', 'b'])->all()
        );

        // Cross join with a collection
        $this->assertEquals(
            [[1, 'a'], [1, 'b'], [2, 'a'], [2, 'b']],
            (new $collection([1, 2]))->crossJoin(new $collection(['a', 'b']))->all()
        );

        // Cross join with 2 collections
        $this->assertEquals(
            [
                [1, 'a', 'I'], [1, 'a', 'II'],
                [1, 'b', 'I'], [1, 'b', 'II'],
                [2, 'a', 'I'], [2, 'a', 'II'],
                [2, 'b', 'I'], [2, 'b', 'II'],
            ],
            (new $collection([1, 2]))->crossJoin(
                new $collection(['a', 'b']),
                new $collection(['I', 'II'])
            )->all()
        );
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSort($collection) {
        $data = (new $collection([5, 3, 1, 2, 4]))->sort();
        $this->assertEquals([1, 2, 3, 4, 5], $data->values()->all());

        $data = (new $collection([-1, -3, -2, -4, -5, 0, 5, 3, 1, 2, 4]))->sort();
        $this->assertEquals([-5, -4, -3, -2, -1, 0, 1, 2, 3, 4, 5], $data->values()->all());

        $data = (new $collection(['foo', 'bar-10', 'bar-1']))->sort();
        $this->assertEquals(['bar-1', 'bar-10', 'foo'], $data->values()->all());

        $data = (new $collection(['T2', 'T1', 'T10']))->sort();
        $this->assertEquals(['T1', 'T10', 'T2'], $data->values()->all());

        $data = (new $collection(['T2', 'T1', 'T10']))->sort(SORT_NATURAL);
        $this->assertEquals(['T1', 'T2', 'T10'], $data->values()->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSortDesc($collection) {
        $data = (new $collection([5, 3, 1, 2, 4]))->sortDesc();
        $this->assertEquals([5, 4, 3, 2, 1], $data->values()->all());

        $data = (new $collection([-1, -3, -2, -4, -5, 0, 5, 3, 1, 2, 4]))->sortDesc();
        $this->assertEquals([5, 4, 3, 2, 1, 0, -1, -2, -3, -4, -5], $data->values()->all());

        $data = (new $collection(['bar-1', 'foo', 'bar-10']))->sortDesc();
        $this->assertEquals(['foo', 'bar-10', 'bar-1'], $data->values()->all());

        $data = (new $collection(['T2', 'T1', 'T10']))->sortDesc();
        $this->assertEquals(['T2', 'T10', 'T1'], $data->values()->all());

        $data = (new $collection(['T2', 'T1', 'T10']))->sortDesc(SORT_NATURAL);
        $this->assertEquals(['T10', 'T2', 'T1'], $data->values()->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSortWithCallback($collection) {
        $data = (new $collection([5, 3, 1, 2, 4]))->sort(function ($a, $b) {
            if ($a === $b) {
                return 0;
            }

            return ($a < $b) ? -1 : 1;
        });

        $this->assertEquals(range(1, 5), array_values($data->all()));
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSortBy($collection) {
        $data = new $collection(['ither', 'dayle']);
        $data = $data->sortBy(function ($x) {
            return $x;
        });

        $this->assertEquals(['dayle', 'ither'], array_values($data->all()));

        $data = new $collection(['dayle', 'ither']);
        $data = $data->sortByDesc(function ($x) {
            return $x;
        });

        $this->assertEquals(['ither', 'dayle'], array_values($data->all()));
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSortByString($collection) {
        $data = new $collection([['name' => 'ither'], ['name' => 'dayle']]);
        $data = $data->sortBy('name', SORT_STRING);

        $this->assertEquals([['name' => 'dayle'], ['name' => 'ither']], array_values($data->all()));

        $data = new $collection([['name' => 'ither'], ['name' => 'dayle']]);
        $data = $data->sortBy('name', SORT_STRING);

        $this->assertEquals([['name' => 'dayle'], ['name' => 'ither']], array_values($data->all()));
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSortByAlwaysReturnsAssoc($collection) {
        $data = new $collection(['a' => 'ither', 'b' => 'dayle']);
        $data = $data->sortBy(function ($x) {
            return $x;
        });

        $this->assertEquals(['b' => 'dayle', 'a' => 'ither'], $data->all());

        $data = new $collection(['ither', 'dayle']);
        $data = $data->sortBy(function ($x) {
            return $x;
        });

        $this->assertEquals([1 => 'dayle', 0 => 'ither'], $data->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSortKeys($collection) {
        $data = new $collection(['b' => 'dayle', 'a' => 'ither']);

        $this->assertSame(['a' => 'ither', 'b' => 'dayle'], $data->sortKeys()->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSortKeysDesc($collection) {
        $data = new $collection(['a' => 'ither', 'b' => 'dayle']);

        $this->assertSame(['b' => 'dayle', 'a' => 'ither'], $data->sortKeysDesc()->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testReverse($collection) {
        $data = new $collection(['zaeed', 'alan']);
        $reversed = $data->reverse();

        $this->assertSame([1 => 'alan', 0 => 'zaeed'], $reversed->all());

        $data = new $collection(['name' => 'ither', 'framework' => 'laravel']);
        $reversed = $data->reverse();

        $this->assertSame(['framework' => 'laravel', 'name' => 'ither'], $reversed->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testFlip($collection) {
        $data = new $collection(['name' => 'ither', 'framework' => 'laravel']);
        $this->assertEquals(['ither' => 'name', 'laravel' => 'framework'], $data->flip()->toArray());
    }
}
