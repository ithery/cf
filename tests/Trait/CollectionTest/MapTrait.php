<?php

trait CollectionTest_MapTrait {
    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testMap($collection) {
        $data = new $collection(['first' => 'ither', 'last' => 'otwell']);
        $data = $data->map(function ($item, $key) {
            return $key . '-' . strrev($item);
        });
        $this->assertEquals(['first' => 'first-rolyat', 'last' => 'last-llewto'], $data->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testMapSpread($collection) {
        $c = new $collection([[1, 'a'], [2, 'b']]);

        $result = $c->mapSpread(function ($number, $character) {
            return "{$number}-{$character}";
        });
        $this->assertEquals(['1-a', '2-b'], $result->all());

        $result = $c->mapSpread(function ($number, $character, $key) {
            return "{$number}-{$character}-{$key}";
        });
        $this->assertEquals(['1-a-0', '2-b-1'], $result->all());

        $c = new $collection([new CCollection([1, 'a']), new CCollection([2, 'b'])]);
        $result = $c->mapSpread(function ($number, $character, $key) {
            return "{$number}-{$character}-{$key}";
        });
        $this->assertEquals(['1-a-0', '2-b-1'], $result->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testFlatMap($collection) {
        $data = new $collection([
            ['name' => 'ither', 'hobbies' => ['programming', 'basketball']],
            ['name' => 'adam', 'hobbies' => ['music', 'powerlifting']],
        ]);
        $data = $data->flatMap(function ($person) {
            return $person['hobbies'];
        });
        $this->assertEquals(['programming', 'basketball', 'music', 'powerlifting'], $data->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testMapToDictionary($collection) {
        $data = new $collection([
            ['id' => 1, 'name' => 'A'],
            ['id' => 2, 'name' => 'B'],
            ['id' => 3, 'name' => 'C'],
            ['id' => 4, 'name' => 'B'],
        ]);

        $groups = $data->mapToDictionary(function ($item, $key) {
            return [$item['name'] => $item['id']];
        });

        $this->assertInstanceOf($collection, $groups);
        $this->assertEquals(['A' => [1], 'B' => [2, 4], 'C' => [3]], $groups->toArray());
        $this->assertIsArray($groups->get('A'));
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testMapToDictionaryWithNumericKeys($collection) {
        $data = new $collection([1, 2, 3, 2, 1]);

        $groups = $data->mapToDictionary(function ($item, $key) {
            return [$item => $key];
        });

        $this->assertEquals([1 => [0, 4], 2 => [1, 3], 3 => [2]], $groups->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testMapToGroups($collection) {
        $data = new $collection([
            ['id' => 1, 'name' => 'A'],
            ['id' => 2, 'name' => 'B'],
            ['id' => 3, 'name' => 'C'],
            ['id' => 4, 'name' => 'B'],
        ]);

        $groups = $data->mapToGroups(function ($item, $key) {
            return [$item['name'] => $item['id']];
        });

        $this->assertInstanceOf($collection, $groups);
        $this->assertEquals(['A' => [1], 'B' => [2, 4], 'C' => [3]], $groups->toArray());
        $this->assertInstanceOf($collection, $groups->get('A'));
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testMapToGroupsWithNumericKeys($collection) {
        $data = new $collection([1, 2, 3, 2, 1]);

        $groups = $data->mapToGroups(function ($item, $key) {
            return [$item => $key];
        });

        $this->assertEquals([1 => [0, 4], 2 => [1, 3], 3 => [2]], $groups->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testMapWithKeys($collection) {
        $data = new $collection([
            ['name' => 'Blastoise', 'type' => 'Water', 'idx' => 9],
            ['name' => 'Charmander', 'type' => 'Fire', 'idx' => 4],
            ['name' => 'Dragonair', 'type' => 'Dragon', 'idx' => 148],
        ]);
        $data = $data->mapWithKeys(function ($pokemon) {
            return [$pokemon['name'] => $pokemon['type']];
        });
        $this->assertEquals(
            ['Blastoise' => 'Water', 'Charmander' => 'Fire', 'Dragonair' => 'Dragon'],
            $data->all()
        );
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testMapWithKeysIntegerKeys($collection) {
        $data = new $collection([
            ['id' => 1, 'name' => 'A'],
            ['id' => 3, 'name' => 'B'],
            ['id' => 2, 'name' => 'C'],
        ]);
        $data = $data->mapWithKeys(function ($item) {
            return [$item['id'] => $item];
        });
        $this->assertSame(
            [1, 3, 2],
            $data->keys()->all()
        );
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testMapWithKeysMultipleRows($collection) {
        $data = new $collection([
            ['id' => 1, 'name' => 'A'],
            ['id' => 2, 'name' => 'B'],
            ['id' => 3, 'name' => 'C'],
        ]);
        $data = $data->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name'], $item['name'] => $item['id']];
        });
        $this->assertSame(
            [
                1 => 'A',
                'A' => 1,
                2 => 'B',
                'B' => 2,
                3 => 'C',
                'C' => 3,
            ],
            $data->all()
        );
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testMapWithKeysCallbackKey($collection) {
        $data = new $collection([
            3 => ['id' => 1, 'name' => 'A'],
            5 => ['id' => 3, 'name' => 'B'],
            4 => ['id' => 2, 'name' => 'C'],
        ]);
        $data = $data->mapWithKeys(function ($item, $key) {
            return [$key => $item['id']];
        });
        $this->assertSame(
            [3, 5, 4],
            $data->keys()->all()
        );
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testMapInto($collection) {
        $data = new $collection([
            'first', 'second',
        ]);

        $data = $data->mapInto(TestCollectionMapIntoObject::class);

        $this->assertSame('first', $data->get(0)->value);
        $this->assertSame('second', $data->get(1)->value);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testNth($collection) {
        $data = new $collection([
            6 => 'a',
            4 => 'b',
            7 => 'c',
            1 => 'd',
            5 => 'e',
            3 => 'f',
        ]);

        $this->assertEquals(['a', 'e'], $data->nth(4)->all());
        $this->assertEquals(['b', 'f'], $data->nth(4, 1)->all());
        $this->assertEquals(['c'], $data->nth(4, 2)->all());
        $this->assertEquals(['d'], $data->nth(4, 3)->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testMapWithKeysOverwritingKeys($collection) {
        $data = new $collection([
            ['id' => 1, 'name' => 'A'],
            ['id' => 2, 'name' => 'B'],
            ['id' => 1, 'name' => 'C'],
        ]);
        $data = $data->mapWithKeys(function ($item) {
            return [$item['id'] => $item['name']];
        });
        $this->assertSame(
            [
                1 => 'C',
                2 => 'B',
            ],
            $data->all()
        );
    }
}
