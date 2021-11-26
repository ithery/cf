<?php
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

// @codingStandardsIgnoreStart
class carrTest extends TestCase {
    //@codingStandardsIgnoreEnd
    /**
     * Test carr::accessible.
     *
     * @return void
     */
    public function testAccessible() {
        $this->assertTrue(carr::accessible([]));
        $this->assertTrue(carr::accessible([1, 2]));
        $this->assertTrue(carr::accessible(['a' => 1, 'b' => 2]));
        $this->assertTrue(carr::accessible(new CCollection()));

        $this->assertFalse(carr::accessible(null));
        $this->assertFalse(carr::accessible('abc'));
        $this->assertFalse(carr::accessible(new stdClass()));
        $this->assertFalse(carr::accessible((object) ['a' => 1, 'b' => 2]));
    }

    public function testAdd() {
        $array = carr::add(['name' => 'Desk'], 'price', 100);
        $this->assertEquals(['name' => 'Desk', 'price' => 100], $array);

        $this->assertEquals(['surname' => 'Mövsümov'], carr::add([], 'surname', 'Mövsümov'));
        $this->assertEquals(['developer' => ['name' => 'Ferid']], carr::add([], 'developer.name', 'Ferid'));
    }

    public function testCollapse() {
        $data = [['foo', 'bar'], ['baz']];
        $this->assertEquals(['foo', 'bar', 'baz'], carr::collapse($data));

        $array = [[1], [2], [3], ['foo', 'bar'], c::collect(['baz', 'boom'])];
        $this->assertEquals([1, 2, 3, 'foo', 'bar', 'baz', 'boom'], carr::collapse($array));
    }

    public function testCrossJoin() {
        // Single dimension
        $this->assertSame(
            [[1, 'a'], [1, 'b'], [1, 'c']],
            carr::crossJoin([1], ['a', 'b', 'c'])
        );

        // Square matrix
        $this->assertSame(
            [[1, 'a'], [1, 'b'], [2, 'a'], [2, 'b']],
            carr::crossJoin([1, 2], ['a', 'b'])
        );

        // Rectangular matrix
        $this->assertSame(
            [[1, 'a'], [1, 'b'], [1, 'c'], [2, 'a'], [2, 'b'], [2, 'c']],
            carr::crossJoin([1, 2], ['a', 'b', 'c'])
        );

        // 3D matrix
        $this->assertSame(
            [
                [1, 'a', 'I'], [1, 'a', 'II'], [1, 'a', 'III'],
                [1, 'b', 'I'], [1, 'b', 'II'], [1, 'b', 'III'],
                [2, 'a', 'I'], [2, 'a', 'II'], [2, 'a', 'III'],
                [2, 'b', 'I'], [2, 'b', 'II'], [2, 'b', 'III'],
            ],
            carr::crossJoin([1, 2], ['a', 'b'], ['I', 'II', 'III'])
        );

        // With 1 empty dimension
        $this->assertEmpty(carr::crossJoin([], ['a', 'b'], ['I', 'II', 'III']));
        $this->assertEmpty(carr::crossJoin([1, 2], [], ['I', 'II', 'III']));
        $this->assertEmpty(carr::crossJoin([1, 2], ['a', 'b'], []));

        // With empty arrays
        $this->assertEmpty(carr::crossJoin([], [], []));
        $this->assertEmpty(carr::crossJoin([], []));
        $this->assertEmpty(carr::crossJoin([]));

        // Not really a proper usage, still, test for preserving BC
        $this->assertSame([[]], carr::crossJoin());
    }

    public function testDivide() {
        list($keys, $values) = carr::divide(['name' => 'Desk']);
        $this->assertEquals(['name'], $keys);
        $this->assertEquals(['Desk'], $values);
    }

    public function testDot() {
        $array = carr::dot(['foo' => ['bar' => 'baz']]);
        $this->assertEquals(['foo.bar' => 'baz'], $array);

        $array = carr::dot([]);
        $this->assertEquals([], $array);

        $array = carr::dot(['foo' => []]);
        $this->assertEquals(['foo' => []], $array);

        $array = carr::dot(['foo' => ['bar' => []]]);
        $this->assertEquals(['foo.bar' => []], $array);

        $array = carr::dot(['name' => 'taylor', 'languages' => ['php' => true]]);
        $this->assertEquals(['name' => 'taylor', 'languages.php' => true], $array);
    }

    public function testUndot() {
        $array = carr::undot([
            'user.name' => 'Taylor',
            'user.age' => 25,
            'user.languages.0' => 'PHP',
            'user.languages.1' => 'C#',
        ]);
        $this->assertEquals(['user' => ['name' => 'Taylor', 'age' => 25, 'languages' => ['PHP', 'C#']]], $array);

        $array = carr::undot([
            'pagination.previous' => '<<',
            'pagination.next' => '>>',
        ]);
        $this->assertEquals(['pagination' => ['previous' => '<<', 'next' => '>>']], $array);

        $array = carr::undot([
            'foo',
            'foo.bar' => 'baz',
            'foo.baz' => ['a' => 'b'],
        ]);
        $this->assertEquals(['foo', 'foo' => ['bar' => 'baz', 'baz' => ['a' => 'b']]], $array);
    }

    public function testExcept() {
        $array = ['name' => 'taylor', 'age' => 26];
        $this->assertEquals(['age' => 26], carr::except($array, ['name']));
        $this->assertEquals(['age' => 26], carr::except($array, 'name'));

        $array = ['name' => 'taylor', 'framework' => ['language' => 'PHP', 'name' => 'Laravel']];
        $this->assertEquals(['name' => 'taylor'], carr::except($array, 'framework'));
        $this->assertEquals(['name' => 'taylor', 'framework' => ['name' => 'Laravel']], carr::except($array, 'framework.language'));
        $this->assertEquals(['framework' => ['language' => 'PHP']], carr::except($array, ['name', 'framework.name']));
    }

    public function testExists() {
        $this->assertTrue(carr::exists([1], 0));
        $this->assertTrue(carr::exists([null], 0));
        $this->assertTrue(carr::exists(['a' => 1], 'a'));
        $this->assertTrue(carr::exists(['a' => null], 'a'));
        $this->assertTrue(carr::exists(new CCollection(['a' => null]), 'a'));

        $this->assertFalse(carr::exists([1], 1));
        $this->assertFalse(carr::exists([null], 1));
        $this->assertFalse(carr::exists(['a' => 1], 0));
        $this->assertFalse(carr::exists(new CCollection(['a' => null]), 'b'));
    }

    public function testFirst() {
        $array = [100, 200, 300];

        // Callback is null and array is empty
        $this->assertNull(carr::first([], null));
        $this->assertSame('foo', carr::first([], null, 'foo'));
        $this->assertSame('bar', carr::first([], null, function () {
            return 'bar';
        }));

        // Callback is null and array is not empty
        $this->assertEquals(100, carr::first($array));

        // Callback is not null and array is not empty
        $value = carr::first($array, function ($value) {
            return $value >= 150;
        });
        $this->assertEquals(200, $value);

        // Callback is not null, array is not empty but no satisfied item
        $value2 = carr::first($array, function ($value) {
            return $value > 300;
        });
        $value3 = carr::first($array, function ($value) {
            return $value > 300;
        }, 'bar');
        $value4 = carr::first($array, function ($value) {
            return $value > 300;
        }, function () {
            return 'baz';
        });
        $this->assertNull($value2);
        $this->assertSame('bar', $value3);
        $this->assertSame('baz', $value4);
    }

    public function testLast() {
        $array = [100, 200, 300];

        $last = carr::last($array, function ($value) {
            return $value < 250;
        });
        $this->assertEquals(200, $last);

        $last = carr::last($array, function ($value, $key) {
            return $key < 2;
        });
        $this->assertEquals(200, $last);

        $this->assertEquals(300, carr::last($array));
    }

    public function testFlatten() {
        // Flat arrays are unaffected
        $array = ['#foo', '#bar', '#baz'];
        $this->assertEquals(['#foo', '#bar', '#baz'], carr::flatten($array));

        // Nested arrays are flattened with existing flat items
        $array = [['#foo', '#bar'], '#baz'];
        $this->assertEquals(['#foo', '#bar', '#baz'], carr::flatten($array));

        // Flattened array includes "null" items
        $array = [['#foo', null], '#baz', null];
        $this->assertEquals(['#foo', null, '#baz', null], carr::flatten($array));

        // Sets of nested arrays are flattened
        $array = [['#foo', '#bar'], ['#baz']];
        $this->assertEquals(['#foo', '#bar', '#baz'], carr::flatten($array));

        // Deeply nested arrays are flattened
        $array = [['#foo', ['#bar']], ['#baz']];
        $this->assertEquals(['#foo', '#bar', '#baz'], carr::flatten($array));

        // Nested arrays are flattened alongside arrays
        $array = [new CCollection(['#foo', '#bar']), ['#baz']];
        $this->assertEquals(['#foo', '#bar', '#baz'], carr::flatten($array));

        // Nested arrays containing plain arrays are flattened
        $array = [new CCollection(['#foo', ['#bar']]), ['#baz']];
        $this->assertEquals(['#foo', '#bar', '#baz'], carr::flatten($array));

        // Nested arrays containing arrays are flattened
        $array = [['#foo', new CCollection(['#bar'])], ['#baz']];
        $this->assertEquals(['#foo', '#bar', '#baz'], carr::flatten($array));

        // Nested arrays containing arrays containing arrays are flattened
        $array = [['#foo', new CCollection(['#bar', ['#zap']])], ['#baz']];
        $this->assertEquals(['#foo', '#bar', '#zap', '#baz'], carr::flatten($array));
    }

    public function testFlattenWithDepth() {
        // No depth flattens recursively
        $array = [['#foo', ['#bar', ['#baz']]], '#zap'];
        $this->assertEquals(['#foo', '#bar', '#baz', '#zap'], carr::flatten($array));

        // Specifying a depth only flattens to that depth
        $array = [['#foo', ['#bar', ['#baz']]], '#zap'];
        $this->assertEquals(['#foo', ['#bar', ['#baz']], '#zap'], carr::flatten($array, 1));

        $array = [['#foo', ['#bar', ['#baz']]], '#zap'];
        $this->assertEquals(['#foo', '#bar', ['#baz'], '#zap'], carr::flatten($array, 2));
    }

    public function testGet() {
        $array = ['products.desk' => ['price' => 100]];
        $this->assertEquals(['price' => 100], carr::get($array, 'products.desk'));

        $array = ['products' => ['desk' => ['price' => 100]]];
        $value = carr::get($array, 'products.desk');
        $this->assertEquals(['price' => 100], $value);

        // Test null array values
        $array = ['foo' => null, 'bar' => ['baz' => null]];
        $this->assertNull(carr::get($array, 'foo', 'default'));
        $this->assertNull(carr::get($array, 'bar.baz', 'default'));

        // Test direct ArrayAccess object
        $array = ['products' => ['desk' => ['price' => 100]]];
        $arrayAccessObject = new ArrayObject($array);
        $value = carr::get($arrayAccessObject, 'products.desk');
        $this->assertEquals(['price' => 100], $value);

        // Test array containing ArrayAccess object
        $arrayAccessChild = new ArrayObject(['products' => ['desk' => ['price' => 100]]]);
        $array = ['child' => $arrayAccessChild];
        $value = carr::get($array, 'child.products.desk');
        $this->assertEquals(['price' => 100], $value);

        // Test array containing multiple nested ArrayAccess objects
        $arrayAccessChild = new ArrayObject(['products' => ['desk' => ['price' => 100]]]);
        $arrayAccessParent = new ArrayObject(['child' => $arrayAccessChild]);
        $array = ['parent' => $arrayAccessParent];
        $value = carr::get($array, 'parent.child.products.desk');
        $this->assertEquals(['price' => 100], $value);

        // Test missing ArrayAccess object field
        $arrayAccessChild = new ArrayObject(['products' => ['desk' => ['price' => 100]]]);
        $arrayAccessParent = new ArrayObject(['child' => $arrayAccessChild]);
        $array = ['parent' => $arrayAccessParent];
        $value = carr::get($array, 'parent.child.desk');
        $this->assertNull($value);

        // Test missing ArrayAccess object field
        $arrayAccessObject = new ArrayObject(['products' => ['desk' => null]]);
        $array = ['parent' => $arrayAccessObject];
        $value = carr::get($array, 'parent.products.desk.price');
        $this->assertNull($value);

        // Test null ArrayAccess object fields
        $array = new ArrayObject(['foo' => null, 'bar' => new ArrayObject(['baz' => null])]);
        $this->assertNull(carr::get($array, 'foo', 'default'));
        $this->assertNull(carr::get($array, 'bar.baz', 'default'));

        // Test null key returns the whole array
        $array = ['foo', 'bar'];
        $this->assertEquals($array, carr::get($array, null));

        // Test $array not an array
        $this->assertSame('default', carr::get(null, 'foo', 'default'));
        $this->assertSame('default', carr::get(false, 'foo', 'default'));

        // Test $array not an array and key is null
        $this->assertSame('default', carr::get(null, null, 'default'));

        // Test $array is empty and key is null
        $this->assertEmpty(carr::get([], null));
        $this->assertEmpty(carr::get([], null, 'default'));

        // Test numeric keys
        $array = [
            'products' => [
                ['name' => 'desk'],
                ['name' => 'chair'],
            ],
        ];
        $this->assertSame('desk', carr::get($array, 'products.0.name'));
        $this->assertSame('chair', carr::get($array, 'products.1.name'));

        // Test return default value for non-existing key.
        $array = ['names' => ['developer' => 'taylor']];
        $this->assertSame('dayle', carr::get($array, 'names.otherDeveloper', 'dayle'));
        $this->assertSame('dayle', carr::get($array, 'names.otherDeveloper', function () {
            return 'dayle';
        }));
    }

    public function testHas() {
        $array = ['products.desk' => ['price' => 100]];
        $this->assertTrue(carr::has($array, 'products.desk'));

        $array = ['products' => ['desk' => ['price' => 100]]];
        $this->assertTrue(carr::has($array, 'products.desk'));
        $this->assertTrue(carr::has($array, 'products.desk.price'));
        $this->assertFalse(carr::has($array, 'products.foo'));
        $this->assertFalse(carr::has($array, 'products.desk.foo'));

        $array = ['foo' => null, 'bar' => ['baz' => null]];
        $this->assertTrue(carr::has($array, 'foo'));
        $this->assertTrue(carr::has($array, 'bar.baz'));

        $array = new ArrayObject(['foo' => 10, 'bar' => new ArrayObject(['baz' => 10])]);
        $this->assertTrue(carr::has($array, 'foo'));
        $this->assertTrue(carr::has($array, 'bar'));
        $this->assertTrue(carr::has($array, 'bar.baz'));
        $this->assertFalse(carr::has($array, 'xxx'));
        $this->assertFalse(carr::has($array, 'xxx.yyy'));
        $this->assertFalse(carr::has($array, 'foo.xxx'));
        $this->assertFalse(carr::has($array, 'bar.xxx'));

        $array = new ArrayObject(['foo' => null, 'bar' => new ArrayObject(['baz' => null])]);
        $this->assertTrue(carr::has($array, 'foo'));
        $this->assertTrue(carr::has($array, 'bar.baz'));

        $array = ['foo', 'bar'];
        $this->assertFalse(carr::has($array, null));

        $this->assertFalse(carr::has(null, 'foo'));
        $this->assertFalse(carr::has(false, 'foo'));

        $this->assertFalse(carr::has(null, null));
        $this->assertFalse(carr::has([], null));

        $array = ['products' => ['desk' => ['price' => 100]]];
        $this->assertTrue(carr::has($array, ['products.desk']));
        $this->assertTrue(carr::has($array, ['products.desk', 'products.desk.price']));
        $this->assertTrue(carr::has($array, ['products', 'products']));
        $this->assertFalse(carr::has($array, ['foo']));
        $this->assertFalse(carr::has($array, []));
        $this->assertFalse(carr::has($array, ['products.desk', 'products.price']));

        $array = [
            'products' => [
                ['name' => 'desk'],
            ],
        ];
        $this->assertTrue(carr::has($array, 'products.0.name'));
        $this->assertFalse(carr::has($array, 'products.0.price'));

        $this->assertFalse(carr::has([], [null]));
        $this->assertFalse(carr::has(null, [null]));

        $this->assertTrue(carr::has(['' => 'some'], ''));
        $this->assertTrue(carr::has(['' => 'some'], ['']));
        $this->assertFalse(carr::has([''], ''));
        $this->assertFalse(carr::has([], ''));
        $this->assertFalse(carr::has([], ['']));
    }

    public function testHasAnyMethod() {
        $array = ['name' => 'Taylor', 'age' => '', 'city' => null];
        $this->assertTrue(carr::hasAny($array, 'name'));
        $this->assertTrue(carr::hasAny($array, 'age'));
        $this->assertTrue(carr::hasAny($array, 'city'));
        $this->assertFalse(carr::hasAny($array, 'foo'));
        $this->assertTrue(carr::hasAny($array, 'name', 'email'));
        $this->assertTrue(carr::hasAny($array, ['name', 'email']));

        $array = ['name' => 'Taylor', 'email' => 'foo'];
        $this->assertTrue(carr::hasAny($array, 'name', 'email'));
        $this->assertFalse(carr::hasAny($array, 'surname', 'password'));
        $this->assertFalse(carr::hasAny($array, ['surname', 'password']));

        $array = ['foo' => ['bar' => null, 'baz' => '']];
        $this->assertTrue(carr::hasAny($array, 'foo.bar'));
        $this->assertTrue(carr::hasAny($array, 'foo.baz'));
        $this->assertFalse(carr::hasAny($array, 'foo.bax'));
        $this->assertTrue(carr::hasAny($array, ['foo.bax', 'foo.baz']));
    }

    public function testIsAssoc() {
        $this->assertTrue(carr::isAssoc(['a' => 'a', 0 => 'b']));
        $this->assertTrue(carr::isAssoc([1 => 'a', 0 => 'b']));
        $this->assertTrue(carr::isAssoc([1 => 'a', 2 => 'b']));
        $this->assertFalse(carr::isAssoc([0 => 'a', 1 => 'b']));
        $this->assertFalse(carr::isAssoc(['a', 'b']));
    }

    public function testIsList() {
        $this->assertTrue(carr::isList([]));
        $this->assertTrue(carr::isList([1, 2, 3]));
        $this->assertTrue(carr::isList(['foo', 2, 3]));
        $this->assertTrue(carr::isList(['foo', 'bar']));
        $this->assertTrue(carr::isList([0 => 'foo', 'bar']));
        $this->assertTrue(carr::isList([0 => 'foo', 1 => 'bar']));

        $this->assertFalse(carr::isList([1 => 'foo', 'bar']));
        $this->assertFalse(carr::isList([1 => 'foo', 0 => 'bar']));
        $this->assertFalse(carr::isList([0 => 'foo', 'bar' => 'baz']));
        $this->assertFalse(carr::isList([0 => 'foo', 2 => 'bar']));
        $this->assertFalse(carr::isList(['foo' => 'bar', 'baz' => 'qux']));
    }

    public function testOnly() {
        $array = ['name' => 'Desk', 'price' => 100, 'orders' => 10];
        $array = carr::only($array, ['name', 'price']);
        $this->assertEquals(['name' => 'Desk', 'price' => 100], $array);
        $this->assertEmpty(carr::only($array, ['nonExistingKey']));
    }

    public function testPluck() {
        $data = [
            'post-1' => [
                'comments' => [
                    'tags' => [
                        '#foo', '#bar',
                    ],
                ],
            ],
            'post-2' => [
                'comments' => [
                    'tags' => [
                        '#baz',
                    ],
                ],
            ],
        ];

        $this->assertEquals([
            0 => [
                'tags' => [
                    '#foo', '#bar',
                ],
            ],
            1 => [
                'tags' => [
                    '#baz',
                ],
            ],
        ], carr::pluck($data, 'comments'));

        $this->assertEquals([['#foo', '#bar'], ['#baz']], carr::pluck($data, 'comments.tags'));
        $this->assertEquals([null, null], carr::pluck($data, 'foo'));
        $this->assertEquals([null, null], carr::pluck($data, 'foo.bar'));

        $array = [
            ['developer' => ['name' => 'Taylor']],
            ['developer' => ['name' => 'Abigail']],
        ];

        $array = carr::pluck($array, 'developer.name');

        $this->assertEquals(['Taylor', 'Abigail'], $array);
    }

    public function testPluckWithArrayValue() {
        $array = [
            ['developer' => ['name' => 'Taylor']],
            ['developer' => ['name' => 'Abigail']],
        ];
        $array = carr::pluck($array, ['developer', 'name']);
        $this->assertEquals(['Taylor', 'Abigail'], $array);
    }

    public function testPluckWithKeys() {
        $array = [
            ['name' => 'Taylor', 'role' => 'developer'],
            ['name' => 'Abigail', 'role' => 'developer'],
        ];

        $test1 = carr::pluck($array, 'role', 'name');
        $test2 = carr::pluck($array, null, 'name');

        $this->assertEquals([
            'Taylor' => 'developer',
            'Abigail' => 'developer',
        ], $test1);

        $this->assertEquals([
            'Taylor' => ['name' => 'Taylor', 'role' => 'developer'],
            'Abigail' => ['name' => 'Abigail', 'role' => 'developer'],
        ], $test2);
    }

    public function testPluckWithCarbonKeys() {
        $array = [
            ['start' => new Carbon('2017-07-25 00:00:00'), 'end' => new Carbon('2017-07-30 00:00:00')],
        ];
        $array = carr::pluck($array, 'end', 'start');
        $this->assertEquals(['2017-07-25 00:00:00' => '2017-07-30 00:00:00'], $array);
    }

    public function testArrayPluckWithArrayAndObjectValues() {
        $array = [(object) ['name' => 'taylor', 'email' => 'foo'], ['name' => 'dayle', 'email' => 'bar']];
        $this->assertEquals(['taylor', 'dayle'], carr::pluck($array, 'name'));
        $this->assertEquals(['taylor' => 'foo', 'dayle' => 'bar'], carr::pluck($array, 'email', 'name'));
    }

    public function testArrayPluckWithNestedKeys() {
        $array = [['user' => ['taylor', 'otwell']], ['user' => ['dayle', 'rees']]];
        $this->assertEquals(['taylor', 'dayle'], carr::pluck($array, 'user.0'));
        $this->assertEquals(['taylor', 'dayle'], carr::pluck($array, ['user', 0]));
        $this->assertEquals(['taylor' => 'otwell', 'dayle' => 'rees'], carr::pluck($array, 'user.1', 'user.0'));
        $this->assertEquals(['taylor' => 'otwell', 'dayle' => 'rees'], carr::pluck($array, ['user', 1], ['user', 0]));
    }

    public function testArrayPluckWithNestedArrays() {
        $array = [
            [
                'account' => 'a',
                'users' => [
                    ['first' => 'taylor', 'last' => 'otwell', 'email' => 'taylorotwell@gmail.com'],
                ],
            ],
            [
                'account' => 'b',
                'users' => [
                    ['first' => 'abigail', 'last' => 'otwell'],
                    ['first' => 'dayle', 'last' => 'rees'],
                ],
            ],
        ];

        $this->assertEquals([['taylor'], ['abigail', 'dayle']], carr::pluck($array, 'users.*.first'));
        $this->assertEquals(['a' => ['taylor'], 'b' => ['abigail', 'dayle']], carr::pluck($array, 'users.*.first', 'account'));
        $this->assertEquals([['taylorotwell@gmail.com'], [null, null]], carr::pluck($array, 'users.*.email'));
    }

    public function testPrepend() {
        $array = carr::prepend(['one', 'two', 'three', 'four'], 'zero');
        $this->assertEquals(['zero', 'one', 'two', 'three', 'four'], $array);

        $array = carr::prepend(['one' => 1, 'two' => 2], 0, 'zero');
        $this->assertEquals(['zero' => 0, 'one' => 1, 'two' => 2], $array);

        $array = carr::prepend(['one' => 1, 'two' => 2], 0, null);
        $this->assertEquals([null => 0, 'one' => 1, 'two' => 2], $array);
    }

    public function testPull() {
        $array = ['name' => 'Desk', 'price' => 100];
        $name = carr::pull($array, 'name');
        $this->assertSame('Desk', $name);
        $this->assertEquals(['price' => 100], $array);

        // Only works on first level keys
        $array = ['joe@example.com' => 'Joe', 'jane@localhost' => 'Jane'];
        $name = carr::pull($array, 'joe@example.com');
        $this->assertSame('Joe', $name);
        $this->assertEquals(['jane@localhost' => 'Jane'], $array);

        // Does not work for nested keys
        $array = ['emails' => ['joe@example.com' => 'Joe', 'jane@localhost' => 'Jane']];
        $name = carr::pull($array, 'emails.joe@example.com');
        $this->assertNull($name);
        $this->assertEquals(['emails' => ['joe@example.com' => 'Joe', 'jane@localhost' => 'Jane']], $array);
    }

    public function testQuery() {
        $this->assertSame('', carr::query([]));
        $this->assertSame('foo=bar', carr::query(['foo' => 'bar']));
        $this->assertSame('foo=bar&bar=baz', carr::query(['foo' => 'bar', 'bar' => 'baz']));
        $this->assertSame('foo=bar&bar=1', carr::query(['foo' => 'bar', 'bar' => true]));
        $this->assertSame('foo=bar', carr::query(['foo' => 'bar', 'bar' => null]));
        $this->assertSame('foo=bar&bar=', carr::query(['foo' => 'bar', 'bar' => '']));
    }

    public function testRandom() {
        $random = carr::random(['foo', 'bar', 'baz']);
        $this->assertContains($random, ['foo', 'bar', 'baz']);

        $random = carr::random(['foo', 'bar', 'baz'], 0);
        $this->assertIsArray($random);
        $this->assertCount(0, $random);

        $random = carr::random(['foo', 'bar', 'baz'], 1);
        $this->assertIsArray($random);
        $this->assertCount(1, $random);
        $this->assertContains($random[0], ['foo', 'bar', 'baz']);

        $random = carr::random(['foo', 'bar', 'baz'], 2);
        $this->assertIsArray($random);
        $this->assertCount(2, $random);
        $this->assertContains($random[0], ['foo', 'bar', 'baz']);
        $this->assertContains($random[1], ['foo', 'bar', 'baz']);

        $random = carr::random(['foo', 'bar', 'baz'], '0');
        $this->assertIsArray($random);
        $this->assertCount(0, $random);

        $random = carr::random(['foo', 'bar', 'baz'], '1');
        $this->assertIsArray($random);
        $this->assertCount(1, $random);
        $this->assertContains($random[0], ['foo', 'bar', 'baz']);

        $random = carr::random(['foo', 'bar', 'baz'], '2');
        $this->assertIsArray($random);
        $this->assertCount(2, $random);
        $this->assertContains($random[0], ['foo', 'bar', 'baz']);
        $this->assertContains($random[1], ['foo', 'bar', 'baz']);

        // preserve keys
        $random = carr::random(['one' => 'foo', 'two' => 'bar', 'three' => 'baz'], 2, true);
        $this->assertIsArray($random);
        $this->assertCount(2, $random);
        $this->assertCount(2, array_intersect_assoc(['one' => 'foo', 'two' => 'bar', 'three' => 'baz'], $random));
    }

    public function testRandomOnEmptyArray() {
        $random = carr::random([], 0);
        $this->assertIsArray($random);
        $this->assertCount(0, $random);

        $random = carr::random([], '0');
        $this->assertIsArray($random);
        $this->assertCount(0, $random);
    }

    public function testRandomThrowsAnErrorWhenRequestingMoreItemsThanAreAvailable() {
        $exceptions = 0;

        try {
            carr::random([]);
        } catch (InvalidArgumentException $e) {
            $exceptions++;
        }

        try {
            carr::random([], 1);
        } catch (InvalidArgumentException $e) {
            $exceptions++;
        }

        try {
            carr::random([], 2);
        } catch (InvalidArgumentException $e) {
            $exceptions++;
        }

        $this->assertSame(3, $exceptions);
    }

    public function testSet() {
        $array = ['products' => ['desk' => ['price' => 100]]];
        carr::set($array, 'products.desk.price', 200);
        $this->assertEquals(['products' => ['desk' => ['price' => 200]]], $array);

        // No key is given
        $array = ['products' => ['desk' => ['price' => 100]]];
        carr::set($array, null, ['price' => 300]);
        $this->assertSame(['price' => 300], $array);

        // The key doesn't exist at the depth
        $array = ['products' => 'desk'];
        carr::set($array, 'products.desk.price', 200);
        $this->assertSame(['products' => ['desk' => ['price' => 200]]], $array);

        // No corresponding key exists
        $array = ['products'];
        carr::set($array, 'products.desk.price', 200);
        $this->assertSame(['products', 'products' => ['desk' => ['price' => 200]]], $array);

        $array = ['products' => ['desk' => ['price' => 100]]];
        carr::set($array, 'table', 500);
        $this->assertSame(['products' => ['desk' => ['price' => 100]], 'table' => 500], $array);

        $array = ['products' => ['desk' => ['price' => 100]]];
        carr::set($array, 'table.price', 350);
        $this->assertSame(['products' => ['desk' => ['price' => 100]], 'table' => ['price' => 350]], $array);

        $array = [];
        carr::set($array, 'products.desk.price', 200);
        $this->assertSame(['products' => ['desk' => ['price' => 200]]], $array);

        // Override
        $array = ['products' => 'table'];
        carr::set($array, 'products.desk.price', 300);
        $this->assertSame(['products' => ['desk' => ['price' => 300]]], $array);
    }

    public function testShuffleWithSeed() {
        $this->assertEquals(
            carr::shuffle(range(0, 100, 10), 1234),
            carr::shuffle(range(0, 100, 10), 1234)
        );
    }

    public function testSort() {
        $unsorted = [
            ['name' => 'Desk'],
            ['name' => 'Chair'],
        ];

        $expected = [
            ['name' => 'Chair'],
            ['name' => 'Desk'],
        ];

        $sorted = array_values(carr::sort($unsorted));
        $this->assertEquals($expected, $sorted);

        // sort with closure
        $sortedWithClosure = array_values(carr::sort($unsorted, function ($value) {
            return $value['name'];
        }));
        $this->assertEquals($expected, $sortedWithClosure);

        // sort with dot notation
        $sortedWithDotNotation = array_values(carr::sort($unsorted, 'name'));
        $this->assertEquals($expected, $sortedWithDotNotation);
    }

    public function testSortRecursive() {
        $array = [
            'users' => [
                [
                    // should sort associative arrays by keys
                    'name' => 'joe',
                    'mail' => 'joe@example.com',
                    // should sort deeply nested arrays
                    'numbers' => [2, 1, 0],
                ],
                [
                    'name' => 'jane',
                    'age' => 25,
                ],
            ],
            'repositories' => [
                // should use weird `sort()` behavior on arrays of arrays
                ['id' => 1],
                ['id' => 0],
            ],
            // should sort non-associative arrays by value
            20 => [2, 1, 0],
            30 => [
                // should sort non-incrementing numerical keys by keys
                2 => 'a',
                1 => 'b',
                0 => 'c',
            ],
        ];

        $expect = [
            20 => [0, 1, 2],
            30 => [
                0 => 'c',
                1 => 'b',
                2 => 'a',
            ],
            'repositories' => [
                ['id' => 0],
                ['id' => 1],
            ],
            'users' => [
                [
                    'age' => 25,
                    'name' => 'jane',
                ],
                [
                    'mail' => 'joe@example.com',
                    'name' => 'joe',
                    'numbers' => [0, 1, 2],
                ],
            ],
        ];

        $this->assertEquals($expect, carr::sortRecursive($array));
    }

    public function testToCssClasses() {
        $classes = carr::toCssClasses([
            'font-bold',
            'mt-4',
        ]);

        $this->assertEquals('font-bold mt-4', $classes);

        $classes = carr::toCssClasses([
            'font-bold',
            'mt-4',
            'ml-2' => true,
            'mr-2' => false,
        ]);

        $this->assertEquals('font-bold mt-4 ml-2', $classes);
    }

    public function testWhere() {
        $array = [100, '200', 300, '400', 500];

        $array = carr::where($array, function ($value, $key) {
            return is_string($value);
        });

        $this->assertEquals([1 => '200', 3 => '400'], $array);
    }

    public function testWhereKey() {
        $array = ['10' => 1, 'foo' => 3, 20 => 2];

        $array = carr::where($array, function ($value, $key) {
            return is_numeric($key);
        });

        $this->assertEquals(['10' => 1, 20 => 2], $array);
    }

    public function testForget() {
        $array = ['products' => ['desk' => ['price' => 100]]];
        carr::forget($array, null);
        $this->assertEquals(['products' => ['desk' => ['price' => 100]]], $array);

        $array = ['products' => ['desk' => ['price' => 100]]];
        carr::forget($array, []);
        $this->assertEquals(['products' => ['desk' => ['price' => 100]]], $array);

        $array = ['products' => ['desk' => ['price' => 100]]];
        carr::forget($array, 'products.desk');
        $this->assertEquals(['products' => []], $array);

        $array = ['products' => ['desk' => ['price' => 100]]];
        carr::forget($array, 'products.desk.price');
        $this->assertEquals(['products' => ['desk' => []]], $array);

        $array = ['products' => ['desk' => ['price' => 100]]];
        carr::forget($array, 'products.final.price');
        $this->assertEquals(['products' => ['desk' => ['price' => 100]]], $array);

        $array = ['shop' => ['cart' => [150 => 0]]];
        carr::forget($array, 'shop.final.cart');
        $this->assertEquals(['shop' => ['cart' => [150 => 0]]], $array);

        $array = ['products' => ['desk' => ['price' => ['original' => 50, 'taxes' => 60]]]];
        carr::forget($array, 'products.desk.price.taxes');
        $this->assertEquals(['products' => ['desk' => ['price' => ['original' => 50]]]], $array);

        $array = ['products' => ['desk' => ['price' => ['original' => 50, 'taxes' => 60]]]];
        carr::forget($array, 'products.desk.final.taxes');
        $this->assertEquals(['products' => ['desk' => ['price' => ['original' => 50, 'taxes' => 60]]]], $array);

        $array = ['products' => ['desk' => ['price' => 50], null => 'something']];
        carr::forget($array, ['products.amount.all', 'products.desk.price']);
        $this->assertEquals(['products' => ['desk' => [], null => 'something']], $array);

        // Only works on first level keys
        $array = ['joe@example.com' => 'Joe', 'jane@example.com' => 'Jane'];
        carr::forget($array, 'joe@example.com');
        $this->assertEquals(['jane@example.com' => 'Jane'], $array);

        // Does not work for nested keys
        $array = ['emails' => ['joe@example.com' => ['name' => 'Joe'], 'jane@localhost' => ['name' => 'Jane']]];
        carr::forget($array, ['emails.joe@example.com', 'emails.jane@localhost']);
        $this->assertEquals(['emails' => ['joe@example.com' => ['name' => 'Joe']]], $array);
    }

    public function testWrap() {
        $string = 'a';
        $array = ['a'];
        $object = new stdClass();
        $object->value = 'a';
        $this->assertEquals(['a'], carr::wrap($string));
        $this->assertEquals($array, carr::wrap($array));
        $this->assertEquals([$object], carr::wrap($object));
        $this->assertEquals([], carr::wrap(null));
        $this->assertEquals([null], carr::wrap([null]));
        $this->assertEquals([null, null], carr::wrap([null, null]));
        $this->assertEquals([''], carr::wrap(''));
        $this->assertEquals([''], carr::wrap(['']));
        $this->assertEquals([false], carr::wrap(false));
        $this->assertEquals([false], carr::wrap([false]));
        $this->assertEquals([0], carr::wrap(0));

        $obj = new stdClass();
        $obj->value = 'a';
        $obj = unserialize(serialize($obj));
        $this->assertEquals([$obj], carr::wrap($obj));
        $this->assertSame($obj, carr::wrap($obj)[0]);
    }

    public function testSortByMany() {
        $unsorted = [
            ['name' => 'John', 'age' => 8,  'meta' => ['key' => 3]],
            ['name' => 'John', 'age' => 10, 'meta' => ['key' => 5]],
            ['name' => 'Dave', 'age' => 10, 'meta' => ['key' => 3]],
            ['name' => 'John', 'age' => 8,  'meta' => ['key' => 2]],
        ];

        // sort using keys
        $sorted = array_values(carr::sort($unsorted, [
            'name',
            'age',
            'meta.key',
        ]));
        $this->assertEquals([
            ['name' => 'Dave', 'age' => 10, 'meta' => ['key' => 3]],
            ['name' => 'John', 'age' => 8,  'meta' => ['key' => 2]],
            ['name' => 'John', 'age' => 8,  'meta' => ['key' => 3]],
            ['name' => 'John', 'age' => 10, 'meta' => ['key' => 5]],
        ], $sorted);

        // sort with order
        $sortedWithOrder = array_values(carr::sort($unsorted, [
            'name',
            ['age', false],
            ['meta.key', true],
        ]));
        $this->assertEquals([
            ['name' => 'Dave', 'age' => 10, 'meta' => ['key' => 3]],
            ['name' => 'John', 'age' => 10, 'meta' => ['key' => 5]],
            ['name' => 'John', 'age' => 8,  'meta' => ['key' => 2]],
            ['name' => 'John', 'age' => 8,  'meta' => ['key' => 3]],
        ], $sortedWithOrder);

        // sort using callable
        $sortedWithCallable = array_values(carr::sort($unsorted, [
            function ($a, $b) {
                return $a['name'] <=> $b['name'];
            },
            function ($a, $b) {
                return $b['age'] <=> $a['age'];
            },
            ['meta.key', true],
        ]));
        $this->assertEquals([
            ['name' => 'Dave', 'age' => 10, 'meta' => ['key' => 3]],
            ['name' => 'John', 'age' => 10, 'meta' => ['key' => 5]],
            ['name' => 'John', 'age' => 8,  'meta' => ['key' => 2]],
            ['name' => 'John', 'age' => 8,  'meta' => ['key' => 3]],
        ], $sortedWithCallable);
    }
}
