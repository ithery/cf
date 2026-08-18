<?php
trait CollectionTest_ContainTrait {
    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testContains($collection) {
        $c = new $collection([1, 3, 5]);

        $this->assertTrue($c->contains(1));
        $this->assertTrue($c->contains('1'));
        $this->assertFalse($c->contains(2));
        $this->assertFalse($c->contains('2'));

        $c = new $collection(['1']);
        $this->assertTrue($c->contains('1'));
        $this->assertTrue($c->contains(1));

        $c = new $collection([null]);
        $this->assertTrue($c->contains(false));
        $this->assertTrue($c->contains(null));
        $this->assertTrue($c->contains([]));
        $this->assertTrue($c->contains(0));
        $this->assertTrue($c->contains(''));

        $c = new $collection([0]);
        $this->assertTrue($c->contains(0));
        $this->assertTrue($c->contains('0'));
        $this->assertTrue($c->contains(false));
        $this->assertTrue($c->contains(null));

        $this->assertTrue($c->contains(function ($value) {
            return $value < 5;
        }));
        $this->assertFalse($c->contains(function ($value) {
            return $value > 5;
        }));

        $c = new $collection([['v' => 1], ['v' => 3], ['v' => 5]]);

        $this->assertTrue($c->contains('v', 1));
        $this->assertFalse($c->contains('v', 2));

        $c = new $collection(['date', 'class', (object) ['foo' => 50]]);

        $this->assertTrue($c->contains('date'));
        $this->assertTrue($c->contains('class'));
        $this->assertFalse($c->contains('foo'));

        $c = new $collection([['a' => false, 'b' => false], ['a' => true, 'b' => false]]);

        $this->assertTrue($c->contains->a);
        $this->assertFalse($c->contains->b);

        $c = new $collection([
            null, 1, 2,
        ]);

        $this->assertTrue($c->contains(function ($value) {
            return is_null($value);
        }));
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testSome($collection) {
        $c = new $collection([1, 3, 5]);

        $this->assertTrue($c->some(1));
        $this->assertFalse($c->some(2));
        $this->assertTrue($c->some(function ($value) {
            return $value < 5;
        }));
        $this->assertFalse($c->some(function ($value) {
            return $value > 5;
        }));

        $c = new $collection([['v' => 1], ['v' => 3], ['v' => 5]]);

        $this->assertTrue($c->some('v', 1));
        $this->assertFalse($c->some('v', 2));

        $c = new $collection(['date', 'class', (object) ['foo' => 50]]);

        $this->assertTrue($c->some('date'));
        $this->assertTrue($c->some('class'));
        $this->assertFalse($c->some('foo'));

        $c = new $collection([['a' => false, 'b' => false], ['a' => true, 'b' => false]]);

        $this->assertTrue($c->some->a);
        $this->assertFalse($c->some->b);

        $c = new $collection([
            null, 1, 2,
        ]);

        $this->assertTrue($c->some(function ($value) {
            return is_null($value);
        }));
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testContainsStrict($collection) {
        $c = new $collection([1, 3, 5, '02']);

        $this->assertTrue($c->containsStrict(1));
        $this->assertFalse($c->containsStrict('1'));
        $this->assertFalse($c->containsStrict(2));
        $this->assertTrue($c->containsStrict('02'));
        $this->assertFalse($c->containsStrict(true));
        $this->assertTrue($c->containsStrict(function ($value) {
            return $value < 5;
        }));
        $this->assertFalse($c->containsStrict(function ($value) {
            return $value > 5;
        }));

        $c = new $collection([0]);
        $this->assertTrue($c->containsStrict(0));
        $this->assertFalse($c->containsStrict('0'));

        $this->assertFalse($c->containsStrict(false));
        $this->assertFalse($c->containsStrict(null));

        $c = new $collection([1, null]);
        $this->assertTrue($c->containsStrict(null));
        $this->assertFalse($c->containsStrict(0));
        $this->assertFalse($c->containsStrict(false));

        $c = new $collection([['v' => 1], ['v' => 3], ['v' => '04'], ['v' => 5]]);

        $this->assertTrue($c->containsStrict('v', 1));
        $this->assertFalse($c->containsStrict('v', 2));
        $this->assertFalse($c->containsStrict('v', '1'));
        $this->assertFalse($c->containsStrict('v', 4));
        $this->assertTrue($c->containsStrict('v', '04'));

        $c = new $collection(['date', 'class', (object) ['foo' => 50], '']);

        $this->assertTrue($c->containsStrict('date'));
        $this->assertTrue($c->containsStrict('class'));
        $this->assertFalse($c->containsStrict('foo'));
        $this->assertFalse($c->containsStrict(null));
        $this->assertTrue($c->containsStrict(''));
    }

    /**
     * @param CCollection $collection
     * @dataProvider collectionClassProvider
     */
    public function testContainsWithOperator($collection) {
        $c = new $collection([['v' => 1], ['v' => 3], ['v' => '4'], ['v' => 5]]);

        $this->assertTrue($c->contains('v', '=', 4));
        $this->assertTrue($c->contains('v', '==', 4));
        $this->assertFalse($c->contains('v', '===', 4));
        $this->assertTrue($c->contains('v', '>', 4));
    }
}
