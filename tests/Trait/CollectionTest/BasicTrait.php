<?php

trait CollectionTest_BasicTrait {
    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testFirstReturnsFirstItemInCollection($collection) {
        /** @var PHPUnit\Framework\TestCase $this */
        $c = new $collection(['foo', 'bar']);
        $this->assertSame('foo', $c->first());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testFirstWithCallback($collection) {
        /** @var PHPUnit\Framework\TestCase $this */
        $data = new $collection(['foo', 'bar', 'baz']);
        $result = $data->first(function ($value) {
            return $value === 'bar';
        });
        $this->assertSame('bar', $result);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testFirstWithCallbackAndDefault($collection) {
        /** @var PHPUnit\Framework\TestCase $this */
        $data = new $collection(['foo', 'bar']);
        $result = $data->first(function ($value) {
            return $value === 'baz';
        }, 'default');
        $this->assertSame('default', $result);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     * @dataProvider collectionClassProvider
     */
    public function testFirstWithDefaultAndWithoutCallback($collection) {
        /** @var PHPUnit\Framework\TestCase $this */
        $data = new $collection();
        $result = $data->first(null, 'default');
        $this->assertSame('default', $result);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testFirstOrFailReturnsFirstItemInCollection($collection) {
        /** @var PHPUnit\Framework\TestCase $this */
        $collection = new $collection([
            ['name' => 'foo'],
            ['name' => 'bar'],
        ]);

        $this->assertSame(['name' => 'foo'], $collection->where('name', 'foo')->firstOrFail());
        $this->assertSame(['name' => 'foo'], $collection->firstOrFail('name', '=', 'foo'));
        $this->assertSame(['name' => 'foo'], $collection->firstOrFail('name', 'foo'));
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testFirstOrFailDoesntThrowExceptionIfMoreThanOneItemExists($collection) {
        /** @var PHPUnit\Framework\TestCase $this */
        $collection = new $collection([
            ['name' => 'foo'],
            ['name' => 'foo'],
            ['name' => 'bar'],
        ]);

        $this->assertSame(['name' => 'foo'], $collection->where('name', 'foo')->firstOrFail());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testFirstOrFailReturnsFirstItemInCollectionIfOnlyOneExistsWithCallback($collection) {
        /** @var PHPUnit\Framework\TestCase $this */
        $data = new $collection(['foo', 'bar', 'baz']);
        $result = $data->firstOrFail(function ($value) {
            return $value === 'bar';
        });
        $this->assertSame('bar', $result);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testFirstOrFailDoesntThrowExceptionIfMoreThanOneItemExistsWithCallback($collection) {
        /** @var PHPUnit\Framework\TestCase $this */
        $data = new $collection(['foo', 'bar', 'bar']);

        $this->assertSame(
            'bar',
            $data->firstOrFail(function ($value) {
                return $value === 'bar';
            })
        );
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testFirstOrFailStopsIteratingAtFirstMatch($collection) {
        /** @var PHPUnit\Framework\TestCase $this */
        $data = new $collection([
            function () {
                return false;
            },
            function () {
                return true;
            },
            function () {
                throw new Exception();
            },
        ]);

        $this->assertNotNull($data->firstOrFail(function ($callback) {
            return $callback();
        }));
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testFirstWhere($collection) {
        /** @var PHPUnit\Framework\TestCase $this */
        $data = new $collection([
            ['material' => 'paper', 'type' => 'book'],
            ['material' => 'rubber', 'type' => 'gasket'],
        ]);

        $this->assertSame('book', $data->firstWhere('material', 'paper')['type']);
        $this->assertSame('gasket', $data->firstWhere('material', 'rubber')['type']);
        $this->assertNull($data->firstWhere('material', 'nonexistent'));
        $this->assertNull($data->firstWhere('nonexistent', 'key'));
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testLastReturnsLastItemInCollection($collection) {
        /** @var PHPUnit\Framework\TestCase $this */
        $c = new $collection(['foo', 'bar']);
        $this->assertSame('bar', $c->last());
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testLastWithCallback($collection) {
        /** @var PHPUnit\Framework\TestCase $this */
        $data = new $collection([100, 200, 300]);
        $result = $data->last(function ($value) {
            return $value < 250;
        });
        $this->assertEquals(200, $result);
        $result = $data->last(function ($value, $key) {
            return $key < 2;
        });
        $this->assertEquals(200, $result);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testLastWithCallbackAndDefault($collection) {
        /** @var PHPUnit\Framework\TestCase $this */
        $data = new $collection(['foo', 'bar']);
        $result = $data->last(function ($value) {
            return $value === 'baz';
        }, 'default');
        $this->assertSame('default', $result);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testLastWithDefaultAndWithoutCallback($collection) {
        /** @var PHPUnit\Framework\TestCase $this */
        $data = new $collection();
        $result = $data->last(null, 'default');
        $this->assertSame('default', $result);
    }
}
