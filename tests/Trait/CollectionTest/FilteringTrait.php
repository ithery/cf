<?php
trait CollectionTest_FilteringTrait {
    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSkipMethod($collection) {
        $data = new $collection([1, 2, 3, 4, 5, 6]);

        // Total items to skip is smaller than collection length
        $this->assertSame([5, 6], $data->skip(4)->values()->all());

        // Total items to skip is more than collection length
        $this->assertSame([], $data->skip(10)->values()->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSkipUntil($collection) {
        $data = new $collection([1, 1, 2, 2, 3, 3, 4, 4]);

        // Item at the beginning of the collection
        $this->assertSame([1, 1, 2, 2, 3, 3, 4, 4], $data->skipUntil(1)->values()->all());

        // Item at the middle of the collection
        $this->assertSame([3, 3, 4, 4], $data->skipUntil(3)->values()->all());

        // Item not in the collection
        $this->assertSame([], $data->skipUntil(5)->values()->all());

        // Item at the beginning of the collection
        $data = $data->skipUntil(function ($value, $key) {
            return $value <= 1;
        })->values();

        $this->assertSame([1, 1, 2, 2, 3, 3, 4, 4], $data->all());

        // Item at the middle of the collection
        $data = $data->skipUntil(function ($value, $key) {
            return $value >= 3;
        })->values();

        $this->assertSame([3, 3, 4, 4], $data->all());

        // Item not in the collection
        $data = $data->skipUntil(function ($value, $key) {
            return $value >= 5;
        })->values();

        $this->assertSame([], $data->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSkipWhile($collection) {
        $data = new $collection([1, 1, 2, 2, 3, 3, 4, 4]);

        // Item at the beginning of the collection
        $this->assertSame([2, 2, 3, 3, 4, 4], $data->skipWhile(1)->values()->all());

        // Item not in the collection
        $this->assertSame([1, 1, 2, 2, 3, 3, 4, 4], $data->skipWhile(5)->values()->all());

        // Item in the collection but not at the beginning
        $this->assertSame([1, 1, 2, 2, 3, 3, 4, 4], $data->skipWhile(2)->values()->all());

        // Item not in the collection
        $data = $data->skipWhile(function ($value, $key) {
            return $value >= 5;
        })->values();

        $this->assertSame([1, 1, 2, 2, 3, 3, 4, 4], $data->all());

        // Item in the collection but not at the beginning
        $data = $data->skipWhile(function ($value, $key) {
            return $value >= 2;
        })->values();

        $this->assertSame([1, 1, 2, 2, 3, 3, 4, 4], $data->all());

        // Item at the beginning of the collection
        $data = $data->skipWhile(function ($value, $key) {
            return $value < 3;
        })->values();

        $this->assertSame([3, 3, 4, 4], $data->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testFilter($collection) {
        $c = new $collection([['id' => 1, 'name' => 'Hello'], ['id' => 2, 'name' => 'World']]);
        $this->assertEquals([1 => ['id' => 2, 'name' => 'World']], $c->filter(function ($item) {
            return $item['id'] == 2;
        })->all());

        $c = new $collection(['', 'Hello', '', 'World']);
        $this->assertEquals(['Hello', 'World'], $c->filter()->values()->toArray());

        $c = new $collection(['id' => 1, 'first' => 'Hello', 'second' => 'World']);
        $this->assertEquals(['first' => 'Hello', 'second' => 'World'], $c->filter(function ($item, $key) {
            return $key !== 'id';
        })->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testHigherOrderKeyBy($collection) {
        $c = new $collection([
            ['id' => 'id1', 'name' => 'first'],
            ['id' => 'id2', 'name' => 'second'],
        ]);

        $this->assertEquals(['id1' => 'first', 'id2' => 'second'], $c->keyBy->id->map->name->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testHigherOrderUnique($collection) {
        $c = new $collection([
            ['id' => '1', 'name' => 'first'],
            ['id' => '1', 'name' => 'second'],
        ]);

        $this->assertCount(1, $c->unique->id);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testHigherOrderFilter($collection) {
        $c = new $collection([
            new class() {
                public $name = 'Alex';

                public function active() {
                    return true;
                }
            },
            new class() {
                public $name = 'John';

                public function active() {
                    return false;
                }
            },
        ]);

        $this->assertCount(1, $c->filter->active());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testWhere($collection) {
        $c = new $collection([['v' => 1], ['v' => 2], ['v' => 3], ['v' => '3'], ['v' => 4]]);

        $this->assertEquals(
            [['v' => 3], ['v' => '3']],
            $c->where('v', 3)->values()->all()
        );
        $this->assertEquals(
            [['v' => 3], ['v' => '3']],
            $c->where('v', '=', 3)->values()->all()
        );
        $this->assertEquals(
            [['v' => 3], ['v' => '3']],
            $c->where('v', '==', 3)->values()->all()
        );
        $this->assertEquals(
            [['v' => 3], ['v' => '3']],
            $c->where('v', 'garbage', 3)->values()->all()
        );
        $this->assertEquals(
            [['v' => 3]],
            $c->where('v', '===', 3)->values()->all()
        );

        $this->assertEquals(
            [['v' => 1], ['v' => 2], ['v' => 4]],
            $c->where('v', '<>', 3)->values()->all()
        );
        $this->assertEquals(
            [['v' => 1], ['v' => 2], ['v' => 4]],
            $c->where('v', '!=', 3)->values()->all()
        );
        $this->assertEquals(
            [['v' => 1], ['v' => 2], ['v' => '3'], ['v' => 4]],
            $c->where('v', '!==', 3)->values()->all()
        );
        $this->assertEquals(
            [['v' => 1], ['v' => 2], ['v' => 3], ['v' => '3']],
            $c->where('v', '<=', 3)->values()->all()
        );
        $this->assertEquals(
            [['v' => 3], ['v' => '3'], ['v' => 4]],
            $c->where('v', '>=', 3)->values()->all()
        );
        $this->assertEquals(
            [['v' => 1], ['v' => 2]],
            $c->where('v', '<', 3)->values()->all()
        );
        $this->assertEquals(
            [['v' => 4]],
            $c->where('v', '>', 3)->values()->all()
        );

        $object = (object) ['foo' => 'bar'];

        $this->assertEquals(
            [],
            $c->where('v', $object)->values()->all()
        );

        $this->assertEquals(
            [['v' => 1], ['v' => 2], ['v' => 3], ['v' => '3'], ['v' => 4]],
            $c->where('v', '<>', $object)->values()->all()
        );

        $this->assertEquals(
            [['v' => 1], ['v' => 2], ['v' => 3], ['v' => '3'], ['v' => 4]],
            $c->where('v', '!=', $object)->values()->all()
        );

        $this->assertEquals(
            [['v' => 1], ['v' => 2], ['v' => 3], ['v' => '3'], ['v' => 4]],
            $c->where('v', '!==', $object)->values()->all()
        );

        $this->assertEquals(
            [],
            $c->where('v', '>', $object)->values()->all()
        );

        $c = new $collection([['v' => 1], ['v' => $object]]);
        $this->assertEquals(
            [['v' => $object]],
            $c->where('v', $object)->values()->all()
        );

        $this->assertEquals(
            [['v' => 1], ['v' => $object]],
            $c->where('v', '<>', null)->values()->all()
        );

        $this->assertEquals(
            [],
            $c->where('v', '<', null)->values()->all()
        );

        $c = new $collection([['v' => 1], ['v' => new CBase_HtmlString('hello')]]);
        $this->assertEquals(
            [['v' => new CBase_HtmlString('hello')]],
            $c->where('v', 'hello')->values()->all()
        );

        $c = new $collection([['v' => 1], ['v' => 'hello']]);
        $this->assertEquals(
            [['v' => 'hello']],
            $c->where('v', new CBase_HtmlString('hello'))->values()->all()
        );

        $c = new $collection([['v' => 1], ['v' => 2], ['v' => null]]);
        $this->assertEquals(
            [['v' => 1], ['v' => 2]],
            $c->where('v')->values()->all()
        );
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testWhereStrict($collection) {
        $c = new $collection([['v' => 3], ['v' => '3']]);

        $this->assertEquals(
            [['v' => 3]],
            $c->whereStrict('v', 3)->values()->all()
        );
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testWhereInstanceOf($collection) {
        $c = new $collection([new stdClass(), new stdClass(), new $collection(), new stdClass(), new cstr()]);
        $this->assertCount(3, $c->whereInstanceOf(stdClass::class));

        $this->assertCount(4, $c->whereInstanceOf([stdClass::class, cstr::class]));
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testWhereIn($collection) {
        $c = new $collection([['v' => 1], ['v' => 2], ['v' => 3], ['v' => '3'], ['v' => 4]]);
        $this->assertEquals([['v' => 1], ['v' => 3], ['v' => '3']], $c->whereIn('v', [1, 3])->values()->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testWhereInStrict($collection) {
        $c = new $collection([['v' => 1], ['v' => 2], ['v' => 3], ['v' => '3'], ['v' => 4]]);
        $this->assertEquals([['v' => 1], ['v' => 3]], $c->whereInStrict('v', [1, 3])->values()->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testWhereNotIn($collection) {
        $c = new $collection([['v' => 1], ['v' => 2], ['v' => 3], ['v' => '3'], ['v' => 4]]);
        $this->assertEquals([['v' => 2], ['v' => 4]], $c->whereNotIn('v', [1, 3])->values()->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testWhereNotInStrict($collection) {
        $c = new $collection([['v' => 1], ['v' => 2], ['v' => 3], ['v' => '3'], ['v' => 4]]);
        $this->assertEquals([['v' => 2], ['v' => '3'], ['v' => 4]], $c->whereNotInStrict('v', [1, 3])->values()->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testValues($collection) {
        $c = new $collection([['id' => 1, 'name' => 'Hello'], ['id' => 2, 'name' => 'World']]);
        $this->assertEquals([['id' => 2, 'name' => 'World']], $c->filter(function ($item) {
            return $item['id'] == 2;
        })->values()->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testBetween($collection) {
        $c = new $collection([['v' => 1], ['v' => 2], ['v' => 3], ['v' => '3'], ['v' => 4]]);

        $this->assertEquals(
            [['v' => 2], ['v' => 3], ['v' => '3'], ['v' => 4]],
            $c->whereBetween('v', [2, 4])->values()->all()
        );
        $this->assertEquals([['v' => 1]], $c->whereBetween('v', [-1, 1])->all());
        $this->assertEquals([['v' => 3], ['v' => '3']], $c->whereBetween('v', [3, 3])->values()->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testWhereNotBetween($collection) {
        $c = new $collection([['v' => 1], ['v' => 2], ['v' => 3], ['v' => '3'], ['v' => 4]]);

        $this->assertEquals([['v' => 1]], $c->whereNotBetween('v', [2, 4])->values()->all());
        $this->assertEquals([['v' => 2], ['v' => 3], ['v' => 3], ['v' => 4]], $c->whereNotBetween('v', [-1, 1])->values()->all());
        $this->assertEquals([['v' => 1], ['v' => '2'], ['v' => '4']], $c->whereNotBetween('v', [3, 3])->values()->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testHigherOrderCollectionGroupBy($collection) {
        $data = new $collection([
            new TestSupportCollectionHigherOrderItem(),
            new TestSupportCollectionHigherOrderItem('TAYLOR'),
            new TestSupportCollectionHigherOrderItem('foo'),
        ]);

        $this->assertEquals([
            'ither' => [$data->get(0)],
            'TAYLOR' => [$data->get(1)],
            'foo' => [$data->get(2)],
        ], $data->groupBy->name->toArray());

        $this->assertEquals([
            'TAYLOR' => [$data->get(0), $data->get(1)],
            'FOO' => [$data->get(2)],
        ], $data->groupBy->uppercase()->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testHigherOrderCollectionMap($collection) {
        $person1 = (object) ['name' => 'Taylor'];
        $person2 = (object) ['name' => 'Yaz'];

        $data = new $collection([$person1, $person2]);

        $this->assertEquals(['Taylor', 'Yaz'], $data->map->name->toArray());

        $data = new $collection([new TestSupportCollectionHigherOrderItem(), new TestSupportCollectionHigherOrderItem()]);

        $this->assertEquals(['TAYLOR', 'TAYLOR'], $data->each->uppercase()->map->name->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testHigherOrderCollectionMapFromArrays($collection) {
        $person1 = ['name' => 'Taylor'];
        $person2 = ['name' => 'Yaz'];

        $data = new $collection([$person1, $person2]);

        $this->assertEquals(['Taylor', 'Yaz'], $data->map->name->toArray());

        $data = new $collection([new TestSupportCollectionHigherOrderItem(), new TestSupportCollectionHigherOrderItem()]);

        $this->assertEquals(['TAYLOR', 'TAYLOR'], $data->each->uppercase()->map->name->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testTap($collection) {
        $data = new $collection([1, 2, 3]);

        $fromTap = [];
        $data = $data->tap(function ($data) use (&$fromTap) {
            $fromTap = $data->slice(0, 1)->toArray();
        });

        $this->assertSame([1], $fromTap);
        $this->assertSame([1, 2, 3], $data->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testWhen($collection) {
        $data = new $collection(['michael', 'tom']);

        $data = $data->when('adam', function ($data, $newName) {
            return $data->concat([$newName]);
        });

        $this->assertSame(['michael', 'tom', 'adam'], $data->toArray());

        $data = new $collection(['michael', 'tom']);

        $data = $data->when(false, function ($data) {
            return $data->concat(['adam']);
        });

        $this->assertSame(['michael', 'tom'], $data->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testWhenDefault($collection) {
        $data = new $collection(['michael', 'tom']);

        $data = $data->when(false, function ($data) {
            return $data->concat(['adam']);
        }, function ($data) {
            return $data->concat(['ither']);
        });

        $this->assertSame(['michael', 'tom', 'ither'], $data->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testWhenEmpty($collection) {
        $data = new $collection(['michael', 'tom']);

        $data = $data->whenEmpty(function ($collection) use ($data) {
            return $data->concat(['adam']);
        });

        $this->assertSame(['michael', 'tom'], $data->toArray());

        $data = new $collection();

        $data = $data->whenEmpty(function ($data) {
            return $data->concat(['adam']);
        });

        $this->assertSame(['adam'], $data->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testWhenEmptyDefault($collection) {
        $data = new $collection(['michael', 'tom']);

        $data = $data->whenEmpty(function ($data) {
            return $data->concat(['adam']);
        }, function ($data) {
            return $data->concat(['ither']);
        });

        $this->assertSame(['michael', 'tom', 'ither'], $data->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testWhenNotEmpty($collection) {
        $data = new $collection(['michael', 'tom']);

        $data = $data->whenNotEmpty(function ($data) {
            return $data->concat(['adam']);
        });

        $this->assertSame(['michael', 'tom', 'adam'], $data->toArray());

        $data = new $collection();

        $data = $data->whenNotEmpty(function ($data) {
            return $data->concat(['adam']);
        });

        $this->assertSame([], $data->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testWhenNotEmptyDefault($collection) {
        $data = new $collection(['michael', 'tom']);

        $data = $data->whenNotEmpty(function ($data) {
            return $data->concat(['adam']);
        }, function ($data) {
            return $data->concat(['ither']);
        });

        $this->assertSame(['michael', 'tom', 'adam'], $data->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testUnless($collection) {
        $data = new $collection(['michael', 'tom']);

        $data = $data->unless(false, function ($data) {
            return $data->concat(['caleb']);
        });

        $this->assertSame(['michael', 'tom', 'caleb'], $data->toArray());

        $data = new $collection(['michael', 'tom']);

        $data = $data->unless(true, function ($data) {
            return $data->concat(['caleb']);
        });

        $this->assertSame(['michael', 'tom'], $data->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testUnlessDefault($collection) {
        $data = new $collection(['michael', 'tom']);

        $data = $data->unless(true, function ($data) {
            return $data->concat(['caleb']);
        }, function ($data) {
            return $data->concat(['ither']);
        });

        $this->assertSame(['michael', 'tom', 'ither'], $data->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testUnlessEmpty($collection) {
        $data = new $collection(['michael', 'tom']);

        $data = $data->unlessEmpty(function ($data) {
            return $data->concat(['adam']);
        });

        $this->assertSame(['michael', 'tom', 'adam'], $data->toArray());

        $data = new $collection();

        $data = $data->unlessEmpty(function ($data) {
            return $data->concat(['adam']);
        });

        $this->assertSame([], $data->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testUnlessEmptyDefault($collection) {
        $data = new $collection(['michael', 'tom']);

        $data = $data->unlessEmpty(function ($data) {
            return $data->concat(['adam']);
        }, function ($data) {
            return $data->concat(['ither']);
        });

        $this->assertSame(['michael', 'tom', 'adam'], $data->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testUnlessNotEmpty($collection) {
        $data = new $collection(['michael', 'tom']);

        $data = $data->unlessNotEmpty(function ($data) {
            return $data->concat(['adam']);
        });

        $this->assertSame(['michael', 'tom'], $data->toArray());

        $data = new $collection();

        $data = $data->unlessNotEmpty(function ($data) {
            return $data->concat(['adam']);
        });

        $this->assertSame(['adam'], $data->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testUnlessNotEmptyDefault($collection) {
        $data = new $collection(['michael', 'tom']);

        $data = $data->unlessNotEmpty(function ($data) {
            return $data->concat(['adam']);
        }, function ($data) {
            return $data->concat(['ither']);
        });

        $this->assertSame(['michael', 'tom', 'ither'], $data->toArray());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testWhereNull($collection) {
        $data = new $collection([
            ['name' => 'Taylor'],
            ['name' => null],
            ['name' => 'Bert'],
            ['name' => false],
            ['name' => ''],
        ]);

        $this->assertSame([
            1 => ['name' => null],
        ], $data->whereNull('name')->all());

        $this->assertSame([], $data->whereNull()->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testWhereNullWithoutKey($collection) {
        $collection = new $collection([1, null, 3, 'null', false, true]);
        $this->assertSame([
            1 => null,
        ], $collection->whereNull()->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testWhereNotNull($collection) {
        $data = new $collection($originalData = [
            ['name' => 'Taylor'],
            ['name' => null],
            ['name' => 'Bert'],
            ['name' => false],
            ['name' => ''],
        ]);

        $this->assertSame([
            0 => ['name' => 'Taylor'],
            2 => ['name' => 'Bert'],
            3 => ['name' => false],
            4 => ['name' => ''],
        ], $data->whereNotNull('name')->all());

        $this->assertSame($originalData, $data->whereNotNull()->all());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testWhereNotNullWithoutKey($collection) {
        $data = new $collection([1, null, 3, 'null', false, true]);

        $this->assertSame([
            0 => 1,
            2 => 3,
            3 => 'null',
            4 => false,
            5 => true,
        ], $data->whereNotNull()->all());
    }
}
