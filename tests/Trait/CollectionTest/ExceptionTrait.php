<?php

trait CollectionTest_ExceptionTrait {
    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSoleReturnsFirstItemInCollectionIfOnlyOneExists($collection) {
        /** @var \PHPUnit\Framework\TestCase $this */
        $collection = new $collection([
            ['name' => 'foo'],
            ['name' => 'bar'],
        ]);

        $this->assertSame(['name' => 'foo'], $collection->where('name', 'foo')->sole());
        $this->assertSame(['name' => 'foo'], $collection->sole('name', '=', 'foo'));
        $this->assertSame(['name' => 'foo'], $collection->sole('name', 'foo'));
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSoleThrowsExceptionIfNoItemsExist($collection) {
        /** @var \PHPUnit\Framework\TestCase $this */
        $this->expectException(ItemNotFoundException::class);

        $collection = new $collection([
            ['name' => 'foo'],
            ['name' => 'bar'],
        ]);

        $collection->where('name', 'INVALID')->sole();
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSoleThrowsExceptionIfMoreThanOneItemExists($collection) {
        /** @var \PHPUnit\Framework\TestCase $this */
        $this->expectException(MultipleItemsFoundException::class);

        $collection = new $collection([
            ['name' => 'foo'],
            ['name' => 'foo'],
            ['name' => 'bar'],
        ]);

        $collection->where('name', 'foo')->sole();
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSoleReturnsFirstItemInCollectionIfOnlyOneExistsWithCallback($collection) {
        /** @var \PHPUnit\Framework\TestCase $this */
        $data = new $collection(['foo', 'bar', 'baz']);
        $result = $data->sole(function ($value) {
            return $value === 'bar';
        });
        $this->assertSame('bar', $result);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSoleThrowsExceptionIfNoItemsExistWithCallback($collection) {
        /** @var \PHPUnit\Framework\TestCase $this */
        $this->expectException(ItemNotFoundException::class);

        $data = new $collection(['foo', 'bar', 'baz']);

        $data->sole(function ($value) {
            return $value === 'invalid';
        });
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSoleThrowsExceptionIfMoreThanOneItemExistsWithCallback($collection) {
        /** @var \PHPUnit\Framework\TestCase $this */
        $this->expectException(MultipleItemsFoundException::class);

        $data = new $collection(['foo', 'bar', 'bar']);

        $data->sole(function ($value) {
            return $value === 'bar';
        });
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testFirstOrFailThrowsExceptionIfNoItemsExist($collection) {
        /** @var \PHPUnit\Framework\TestCase $this */
        $this->expectException(ItemNotFoundException::class);

        $collection = new $collection([
            ['name' => 'foo'],
            ['name' => 'bar'],
        ]);

        $collection->where('name', 'INVALID')->firstOrFail();
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testFirstOrFailThrowsExceptionIfNoItemsExistWithCallback($collection) {
        /** @var \PHPUnit\Framework\TestCase $this */
        $this->expectException(ItemNotFoundException::class);

        $data = new $collection(['foo', 'bar', 'baz']);

        $data->firstOrFail(function ($value) {
            return $value === 'invalid';
        });
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testReduceSpreadThrowsAnExceptionIfReducerDoesNotReturnAnArray($collection) {
        /** @var \PHPUnit\Framework\TestCase $this */
        $data = new $collection([1]);

        $this->expectException(UnexpectedValueException::class);

        $data->reduceSpread(function () {
            return false;
        }, null);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testRandomThrowsAnExceptionUsingAmountBiggerThanCollectionSize($collection) {
        /** @var \PHPUnit\Framework\TestCase $this */
        $this->expectException(InvalidArgumentException::class);

        $data = new $collection([1, 2, 3]);
        $data->random(4);
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testItThrowsExceptionWhenTryingToAccessNoProxyProperty($collection) {
        /** @var \PHPUnit\Framework\TestCase $this */
        $data = new $collection();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Property [foo] does not exist on this collection instance.');
        $data->foo;
    }
}
