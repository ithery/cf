<?php

trait CollectionTest_LazyCollectionTrait {
    public function testLazyReturnsLazyCollection() {
        $data = new CCollection([1, 2, 3, 4, 5]);

        $lazy = $data->lazy();

        $data->add(6);

        $this->assertInstanceOf(LazyCollection::class, $lazy);
        $this->assertSame([1, 2, 3, 4, 5], $lazy->all());
    }
}
