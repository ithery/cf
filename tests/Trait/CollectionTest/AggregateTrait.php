<?php

trait CollectionTest_AggregateTrait {
    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testGettingSumFromCollection($collection) {
        $c = new $collection([(object) ['foo' => 50], (object) ['foo' => 50]]);
        $this->assertEquals(100, $c->sum('foo'));

        $c = new $collection([(object) ['foo' => 50], (object) ['foo' => 50]]);
        $this->assertEquals(100, $c->sum(function ($i) {
            return $i->foo;
        }));
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testCanSumValuesWithoutACallback($collection) {
        $c = new $collection([1, 2, 3, 4, 5]);
        $this->assertEquals(15, $c->sum());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testGettingSumFromEmptyCollection($collection) {
        $c = new $collection();
        $this->assertEquals(0, $c->sum('foo'));
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testGettingAvgItemsFromCollection($collection) {
        $c = new $collection([(object) ['foo' => 10], (object) ['foo' => 20]]);
        $this->assertEquals(15, $c->avg(function ($item) {
            return $item->foo;
        }));
        $this->assertEquals(15, $c->avg('foo'));
        $this->assertEquals(15, $c->avg->foo);

        $c = new $collection([(object) ['foo' => 10], (object) ['foo' => 20], (object) ['foo' => null]]);
        $this->assertEquals(15, $c->avg(function ($item) {
            return $item->foo;
        }));
        $this->assertEquals(15, $c->avg('foo'));
        $this->assertEquals(15, $c->avg->foo);

        $c = new $collection([['foo' => 10], ['foo' => 20]]);
        $this->assertEquals(15, $c->avg('foo'));
        $this->assertEquals(15, $c->avg->foo);

        $c = new $collection([1, 2, 3, 4, 5]);
        $this->assertEquals(3, $c->avg());

        $c = new $collection();
        $this->assertNull($c->avg());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testGroupByAttributePreservingKeys($collection) {
        $data = new $collection([10 => ['rating' => 1, 'url' => '1'],  20 => ['rating' => 1, 'url' => '1'],  30 => ['rating' => 2, 'url' => '2']]);

        $result = $data->groupBy('rating', true);

        $expected_result = [
            1 => [10 => ['rating' => 1, 'url' => '1'], 20 => ['rating' => 1, 'url' => '1']],
            2 => [30 => ['rating' => 2, 'url' => '2']],
        ];

        $this->assertEquals($expected_result, $result->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testGroupByClosureWhereItemsHaveSingleGroup($collection) {
        $data = new $collection([['rating' => 1, 'url' => '1'], ['rating' => 1, 'url' => '1'], ['rating' => 2, 'url' => '2']]);

        $result = $data->groupBy(function ($item) {
            return $item['rating'];
        });

        $this->assertEquals([1 => [['rating' => 1, 'url' => '1'], ['rating' => 1, 'url' => '1']], 2 => [['rating' => 2, 'url' => '2']]], $result->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testGroupByClosureWhereItemsHaveSingleGroupPreservingKeys($collection) {
        $data = new $collection([10 => ['rating' => 1, 'url' => '1'], 20 => ['rating' => 1, 'url' => '1'], 30 => ['rating' => 2, 'url' => '2']]);

        $result = $data->groupBy(function ($item) {
            return $item['rating'];
        }, true);

        $expected_result = [
            1 => [10 => ['rating' => 1, 'url' => '1'], 20 => ['rating' => 1, 'url' => '1']],
            2 => [30 => ['rating' => 2, 'url' => '2']],
        ];

        $this->assertEquals($expected_result, $result->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testGroupByClosureWhereItemsHaveMultipleGroups($collection) {
        $data = new $collection([
            ['user' => 1, 'roles' => ['Role_1', 'Role_3']],
            ['user' => 2, 'roles' => ['Role_1', 'Role_2']],
            ['user' => 3, 'roles' => ['Role_1']],
        ]);

        $result = $data->groupBy(function ($item) {
            return $item['roles'];
        });

        $expected_result = [
            'Role_1' => [
                ['user' => 1, 'roles' => ['Role_1', 'Role_3']],
                ['user' => 2, 'roles' => ['Role_1', 'Role_2']],
                ['user' => 3, 'roles' => ['Role_1']],
            ],
            'Role_2' => [
                ['user' => 2, 'roles' => ['Role_1', 'Role_2']],
            ],
            'Role_3' => [
                ['user' => 1, 'roles' => ['Role_1', 'Role_3']],
            ],
        ];

        $this->assertEquals($expected_result, $result->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testGroupByClosureWhereItemsHaveMultipleGroupsPreservingKeys($collection) {
        $data = new $collection([
            10 => ['user' => 1, 'roles' => ['Role_1', 'Role_3']],
            20 => ['user' => 2, 'roles' => ['Role_1', 'Role_2']],
            30 => ['user' => 3, 'roles' => ['Role_1']],
        ]);

        $result = $data->groupBy(function ($item) {
            return $item['roles'];
        }, true);

        $expected_result = [
            'Role_1' => [
                10 => ['user' => 1, 'roles' => ['Role_1', 'Role_3']],
                20 => ['user' => 2, 'roles' => ['Role_1', 'Role_2']],
                30 => ['user' => 3, 'roles' => ['Role_1']],
            ],
            'Role_2' => [
                20 => ['user' => 2, 'roles' => ['Role_1', 'Role_2']],
            ],
            'Role_3' => [
                10 => ['user' => 1, 'roles' => ['Role_1', 'Role_3']],
            ],
        ];

        $this->assertEquals($expected_result, $result->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testGroupByMultiLevelAndClosurePreservingKeys($collection) {
        $data = new $collection([
            10 => ['user' => 1, 'skilllevel' => 1, 'roles' => ['Role_1', 'Role_3']],
            20 => ['user' => 2, 'skilllevel' => 1, 'roles' => ['Role_1', 'Role_2']],
            30 => ['user' => 3, 'skilllevel' => 2, 'roles' => ['Role_1']],
            40 => ['user' => 4, 'skilllevel' => 2, 'roles' => ['Role_2']],
        ]);

        $result = $data->groupBy([
            'skilllevel',
            function ($item) {
                return $item['roles'];
            },
        ], true);

        $expected_result = [
            1 => [
                'Role_1' => [
                    10 => ['user' => 1, 'skilllevel' => 1, 'roles' => ['Role_1', 'Role_3']],
                    20 => ['user' => 2, 'skilllevel' => 1, 'roles' => ['Role_1', 'Role_2']],
                ],
                'Role_3' => [
                    10 => ['user' => 1, 'skilllevel' => 1, 'roles' => ['Role_1', 'Role_3']],
                ],
                'Role_2' => [
                    20 => ['user' => 2, 'skilllevel' => 1, 'roles' => ['Role_1', 'Role_2']],
                ],
            ],
            2 => [
                'Role_1' => [
                    30 => ['user' => 3, 'skilllevel' => 2, 'roles' => ['Role_1']],
                ],
                'Role_2' => [
                    40 => ['user' => 4, 'skilllevel' => 2, 'roles' => ['Role_2']],
                ],
            ],
        ];

        $this->assertEquals($expected_result, $result->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testGroupByAttribute($collection) {
        $data = new $collection([['rating' => 1, 'url' => '1'], ['rating' => 1, 'url' => '1'], ['rating' => 2, 'url' => '2']]);

        $result = $data->groupBy('rating');
        $this->assertEquals([1 => [['rating' => 1, 'url' => '1'], ['rating' => 1, 'url' => '1']], 2 => [['rating' => 2, 'url' => '2']]], $result->toArray());

        $result = $data->groupBy('url');
        $this->assertEquals([1 => [['rating' => 1, 'url' => '1'], ['rating' => 1, 'url' => '1']], 2 => [['rating' => 2, 'url' => '2']]], $result->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testGroupByCallable($collection) {
        $data = new $collection([['rating' => 1, 'url' => '1'], ['rating' => 1, 'url' => '1'], ['rating' => 2, 'url' => '2']]);

        $result = $data->groupBy([$this, 'sortByRating']);
        $this->assertEquals([1 => [['rating' => 1, 'url' => '1'], ['rating' => 1, 'url' => '1']], 2 => [['rating' => 2, 'url' => '2']]], $result->toArray());

        $result = $data->groupBy([$this, 'sortByUrl']);
        $this->assertEquals([1 => [['rating' => 1, 'url' => '1'], ['rating' => 1, 'url' => '1']], 2 => [['rating' => 2, 'url' => '2']]], $result->toArray());
    }

    public function sortByRating(array $value) {
        return $value['rating'];
    }

    public function sortByUrl(array $value) {
        return $value['url'];
    }
}
