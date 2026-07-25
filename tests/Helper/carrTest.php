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

    // ------------------------------------------------------------------
    // Newly added coverage below (aliases, dot-path helpers, misc utils)
    // ------------------------------------------------------------------

    public function testIsAssocAliasMatchesIsAssoc() {
        $this->assertTrue(carr::is_assoc(['a' => 'a', 0 => 'b']));
        $this->assertTrue(carr::is_assoc([1 => 'a', 2 => 'b']));
        $this->assertFalse(carr::is_assoc(['a', 'b']));
        $this->assertSame(carr::isAssoc(['x' => 1]), carr::is_assoc(['x' => 1]));
    }

    public function testIsArrayAliasMatchesIsArray() {
        $this->assertTrue(carr::isArray([]));
        $this->assertTrue(carr::isArray(new ArrayObject([1, 2])));
        $this->assertFalse(carr::isArray('not an array'));
        $this->assertFalse(carr::isArray(false));

        $this->assertTrue(carr::is_array([]));
        $this->assertTrue(carr::is_array(new ArrayObject([1, 2])));
        $this->assertFalse(carr::is_array('not an array'));
        $this->assertFalse(carr::is_array(false));
    }

    public function testPathIsAliasOfGet() {
        $array = ['products' => ['desk' => ['price' => 100]]];
        $this->assertEquals(carr::get($array, 'products.desk'), carr::path($array, 'products.desk'));
        $this->assertSame('default', carr::path($array, 'products.chair', 'default'));
    }

    public function testSetPathIsAliasOfSet() {
        $array = ['products' => ['desk' => ['price' => 100]]];
        carr::set_path($array, 'products.desk.price', 200);
        $this->assertEquals(['products' => ['desk' => ['price' => 200]]], $array);
    }

    public function testCallbackString() {
        $this->assertSame(['limit', ['10', '20']], carr::callback_string('limit[10,20]'));
        $this->assertSame(['required', null], carr::callback_string('required'));
        $this->assertSame(['min', ['5']], carr::callback_string('min[5]'));

        // Escaped commas inside a single param are unescaped after splitting.
        $this->assertSame(['foo', ['a,b', 'c']], carr::callback_string('foo[a\,b,c]'));
    }

    public function testRotate() {
        $source = [
            'row1' => ['a' => 1, 'b' => 2],
            'row2' => ['a' => 3, 'b' => 4],
        ];
        $expected = [
            'a' => ['row1' => 1, 'row2' => 3],
            'b' => ['row1' => 2, 'row2' => 4],
        ];
        $this->assertEquals($expected, carr::rotate($source));

        // With keep_keys = false, sub-array values are re-indexed before rotating.
        $source2 = [
            'row1' => [1, 2],
            'row2' => [3, 4],
        ];
        $expected2 = [
            0 => ['row1' => 1, 'row2' => 3],
            1 => ['row1' => 2, 'row2' => 4],
        ];
        $this->assertEquals($expected2, carr::rotate($source2, false));
    }

    public function testRemove() {
        $array = ['name' => 'Desk', 'price' => 100];
        $value = carr::remove('name', $array);
        $this->assertSame('Desk', $value);
        $this->assertEquals(['price' => 100], $array);

        $this->assertNull(carr::remove('missing', $array));
        $this->assertEquals(['price' => 100], $array);
    }

    public function testExtract() {
        $data = ['level1' => ['level2a' => 'value1', 'level2b' => 'value2']];

        $result = carr::extract($data, ['level1.level2a', 'password']);
        $this->assertEquals(['level1' => ['level2a' => 'value1'], 'password' => null], $result);

        $result2 = carr::extract($data, ['level1.level2a', 'password'], 'N/A');
        $this->assertEquals(['level1' => ['level2a' => 'value1'], 'password' => 'N/A'], $result2);
    }

    public function testMerge() {
        $john = ['name' => 'john', 'children' => ['fred', 'paul', 'sally', 'jane']];
        $mary = ['name' => 'mary', 'children' => ['jane']];
        $merged = carr::merge($john, $mary);
        $this->assertSame(['name' => 'mary', 'children' => ['fred', 'paul', 'sally', 'jane']], $merged);

        // Indexed (list) arrays are appended, skipping values already present.
        $this->assertSame([1, 2, 3], carr::merge([1, 2], [2, 3]));

        // More than two arrays may be merged at once.
        $result = carr::merge(['a' => 1], ['b' => 2], ['c' => 3]);
        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3], $result);
    }

    public function testOverwrite() {
        $a1 = ['name' => 'john', 'mood' => 'happy', 'food' => 'bacon'];
        $a2 = ['name' => 'jack', 'food' => 'tacos', 'drink' => 'beer'];

        // "drink" does not exist on $a1, so it is not added.
        $result = carr::overwrite($a1, $a2);
        $this->assertSame(['name' => 'jack', 'mood' => 'happy', 'food' => 'tacos'], $result);

        $a3 = ['name' => 'jill'];
        $result2 = carr::overwrite($a1, $a2, $a3);
        $this->assertSame(['name' => 'jill', 'mood' => 'happy', 'food' => 'tacos'], $result2);
    }

    public function testUnshiftAssoc() {
        $array = ['b' => 2, 'c' => 3];
        $result = carr::unshift_assoc($array, 'a', 1);
        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3], $result);
        // Passed by reference, so the original is mutated too.
        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3], $array);
    }

    public function testMapRecursiveAndItsAlias() {
        $array = ['a' => 1, 'b' => ['c' => 2, 'd' => 3]];
        $double = function ($v) {
            return $v * 2;
        };
        $expected = ['a' => 2, 'b' => ['c' => 4, 'd' => 6]];

        $this->assertSame($expected, carr::mapRecursive($double, $array));
        // map_recursive is a straight alias of mapRecursive.
        $this->assertSame($expected, carr::map_recursive($double, $array));
    }

    public function testBinarySearch() {
        $haystack = [1, 3, 5, 7, 9];
        $this->assertSame(2, carr::binary_search(5, $haystack));
        $this->assertFalse(carr::binary_search(4, $haystack));
        $this->assertSame(0, carr::binary_search(1, $haystack));
        $this->assertSame(4, carr::binary_search(9, $haystack));

        // Unsorted haystack requires $sort = true to work correctly.
        $unsorted = [9, 1, 5, 3, 7];
        $this->assertSame(2, carr::binary_search(5, $unsorted, true));
    }

    public function testRange() {
        $this->assertSame([10 => 10, 20 => 20, 30 => 30], carr::range(10, 30));
        $this->assertSame([1 => 1, 2 => 2, 3 => 3], carr::range(1, 3));
        // A step less than 1 yields an empty array.
        $this->assertSame([], carr::range(0, 100));
        $this->assertSame([], carr::range(-1, 100));
    }

    public function testToObject() {
        $array = ['name' => 'John', 'address' => ['city' => 'NYC']];
        $object = carr::to_object($array);

        $this->assertInstanceOf(stdClass::class, $object);
        $this->assertSame('John', $object->name);
        $this->assertInstanceOf(stdClass::class, $object->address);
        $this->assertSame('NYC', $object->address->city);
    }

    public function testReplace() {
        $result = carr::replace(['a' => 1, 'b' => 2], ['b' => 3, 'c' => 4]);
        $this->assertSame(['a' => 1, 'b' => 3, 'c' => 4], $result);

        $result2 = carr::replace(['a' => 1], ['b' => 2], ['a' => 3]);
        $this->assertSame(['a' => 3, 'b' => 2], $result2);

        // A non-array argument triggers an E_USER_WARNING and returns null.
        $this->assertNull(@carr::replace(['a' => 1], 'not-an-array'));
    }

    public function testHash() {
        $hash1 = carr::hash([3, 1, 2]);
        $hash2 = carr::hash([1, 2, 3]);
        // array_multisort() means order does not matter for the hash.
        $this->assertSame($hash1, $hash2);
        $this->assertSame(32, strlen($hash1));

        $hash3 = carr::hash([1, 2, 4]);
        $this->assertNotSame($hash1, $hash3);
    }

    public function testHead() {
        $this->assertSame(100, carr::head([100, 200, 300]));
        $this->assertSame('a', carr::head(['x' => 'a', 'y' => 'b']));
        $this->assertFalse(carr::head([]));
    }

    public function testImplode() {
        $array = ['a' => 1, 'b' => 2];
        $this->assertSame('a=1&b=2', carr::implode('=', '&', $array));

        // Nested arrays are flattened per value using carr::implodes(',', ...).
        $nested = ['a' => [1, 2], 'b' => 3];
        $this->assertSame('a=1,2&b=3', carr::implode('=', '&', $nested));

        // A non-array third argument is simply returned untouched.
        $this->assertSame('not-an-array', carr::implode('=', '&', 'not-an-array'));
    }

    public function testImplodes() {
        $this->assertSame('a,b,c', carr::implodes(',', ['a', 'b', 'c']));
        $this->assertSame('not-an-array', carr::implodes(',', 'not-an-array'));
        $this->assertSame('', carr::implodes(',', []));

        // Nested arrays are recursively imploded with the same glue.
        $nested = ['x', ['y', 'z']];
        $this->assertSame('x,y,z', carr::implodes(',', $nested));
    }

    public function testInArrayWildcard() {
        $this->assertTrue(carr::inArrayWildcard('foo.bar', ['foo.*', 'baz.*']));
        $this->assertTrue(carr::inArrayWildcard('foo', ['foo']));
        $this->assertFalse(carr::inArrayWildcard('qux', ['foo.*', 'baz.*']));
        $this->assertFalse(carr::inArrayWildcard('foo', []));
    }

    public function testIsIterable() {
        $this->assertTrue(carr::isIterable([]));
        $this->assertTrue(carr::isIterable([1, 2, 3]));
        $this->assertTrue(carr::isIterable(new ArrayObject()));
        $this->assertFalse(carr::isIterable('string'));
        $this->assertFalse(carr::isIterable(null));
        $this->assertFalse(carr::isIterable(123));
    }

    public function testCount() {
        $this->assertSame(3, carr::count([1, 2, 3]));
        $this->assertSame(0, carr::count([]));
        $this->assertSame(2, carr::count(['a' => 1, 'b' => 2]));
    }

    public function testSortDesc() {
        $array = ['a' => 3, 'b' => 1, 'c' => 2];
        $this->assertSame(['a' => 3, 'c' => 2, 'b' => 1], carr::sortDesc($array));

        $sortedWithCallback = carr::sortDesc($array, function ($value) {
            return $value;
        });
        $this->assertSame(['a' => 3, 'c' => 2, 'b' => 1], $sortedWithCallback);
    }

    public function testToCssStyles() {
        $styles = carr::toCssStyles([
            'background-color: red',
            'display: none' => true,
            'font-weight: bold' => false,
        ]);

        $this->assertSame('background-color: red; display: none;', $styles);
    }

    public function testWhereNotNull() {
        $array = ['a' => 1, 'b' => null, 'c' => 0, 'd' => false, 'e' => ''];
        $this->assertSame(['a' => 1, 'c' => 0, 'd' => false, 'e' => ''], carr::whereNotNull($array));
    }

    public function testArrayMergeRecursiveDistinct() {
        $array1 = ['a' => 1, 'b' => ['x' => 1, 'y' => 2]];
        $array2 = ['b' => ['y' => 3, 'z' => 4], 'c' => 5];
        $expected = ['a' => 1, 'b' => ['x' => 1, 'y' => 3, 'z' => 4], 'c' => 5];

        $this->assertSame($expected, carr::arrayMergeRecursiveDistinct($array1, $array2));

        // Unlike carr::merge(), list values are merged key-by-key (not
        // appended/re-indexed), so only the overlapping index is replaced.
        $list1 = ['tags' => ['a', 'b']];
        $list2 = ['tags' => ['c']];
        $this->assertSame(['tags' => ['c', 'b']], carr::arrayMergeRecursiveDistinct($list1, $list2));
    }

    public function testMirror() {
        $this->assertSame(['a' => 'a', 'b' => 'b'], carr::mirror(['a', 'b']));
        $this->assertSame([], carr::mirror([]));
    }

    public function testTranspose() {
        // transpose() expects a numerically (0-based) keyed list of rows.
        $array = [
            0 => [1, 2, 3],
            1 => [4, 5, 6],
        ];
        $expected = [
            [0 => 1, 1 => 4],
            [0 => 2, 1 => 5],
            [0 => 3, 1 => 6],
        ];
        $this->assertSame($expected, carr::transpose($array));

        // Special-cased when the outer array has a single row: each element
        // of that row is wrapped into its own single-item array.
        $single = ['only' => [1, 2, 3]];
        $this->assertSame([[1], [2], [3]], carr::transpose($single));
    }

    public function testReduce() {
        $sum = carr::reduce([1, 2, 3, 4], function ($carry, $item) {
            return $carry + $item;
        }, 0);
        $this->assertSame(10, $sum);

        // Without an explicit accumulator, the first element seeds it and is
        // not visited again.
        $sum2 = carr::reduce([1, 2, 3, 4], function ($carry, $item) {
            return $carry + $item;
        });
        $this->assertSame(10, $sum2);

        $this->assertNull(carr::reduce(null, function ($c, $i) {
            return $c;
        }, 0));

        $concatenated = carr::reduce(['a' => 1, 'b' => 2], function ($result, $value, $key) {
            $result[] = "{$key}:{$value}";

            return $result;
        }, []);
        $this->assertSame(['a:1', 'b:2'], $concatenated);
    }

    public function testFilter() {
        $result = carr::filter([1, 2, 3, 4, 5], function ($v) {
            return $v % 2 === 0;
        });
        $this->assertSame([1 => 2, 3 => 4], $result);

        $users = [
            ['user' => 'barney', 'active' => true],
            ['user' => 'fred', 'active' => false],
        ];

        // The `matches` iteratee shorthand.
        $result2 = array_values(carr::filter($users, ['active' => true]));
        $this->assertSame([['user' => 'barney', 'active' => true]], $result2);

        // The `property` iteratee shorthand.
        $result3 = array_values(carr::filter($users, 'active'));
        $this->assertSame([['user' => 'barney', 'active' => true]], $result3);
    }

    public function testFind() {
        $users = [
            ['user' => 'barney', 'age' => 36, 'active' => true],
            ['user' => 'fred', 'age' => 40, 'active' => false],
            ['user' => 'pebbles', 'age' => 1, 'active' => true],
        ];

        $result = carr::find($users, function ($o) {
            return $o['age'] < 40;
        });
        $this->assertSame($users[0], $result);

        $result2 = carr::find($users, function ($o) {
            return $o['age'] > 100;
        });
        $this->assertNull($result2);

        // fromIndex skips leading elements.
        $result3 = carr::find($users, function ($o) {
            return $o['age'] < 40;
        }, 1);
        $this->assertSame($users[2], $result3);
    }

    public function testFindLastIndex() {
        $users = [
            ['user' => 'barney', 'active' => true],
            ['user' => 'fred', 'active' => false],
            ['user' => 'pebbles', 'active' => false],
        ];

        $index = carr::findLastIndex($users, function ($u) {
            return $u['user'] === 'pebbles';
        });
        $this->assertSame(2, $index);

        $index2 = carr::findLastIndex($users, function ($u) {
            return $u['active'] === false;
        });
        $this->assertSame(2, $index2);

        $index3 = carr::findLastIndex($users, function ($u) {
            return $u['user'] === 'wilma';
        });
        $this->assertSame(-1, $index3);
    }

    public function testMap() {
        $result = carr::map([1, 2, 3], function ($v) {
            return $v * $v;
        });
        $this->assertSame([1, 4, 9], $result);

        $users = [['user' => 'barney'], ['user' => 'fred']];
        // The `property` iteratee shorthand.
        $result2 = carr::map($users, 'user');
        $this->assertSame(['barney', 'fred'], $result2);
    }

    public function testMapTransform() {
        $result = carr::mapTransform(['hello', 'world'], 'uppercase');
        $this->assertSame(['HELLO', 'WORLD'], $result);
    }

    public function testConcat() {
        $array = [1];
        $other = carr::concat($array, 2, [3], [[4]]);
        $this->assertSame([1, 2, 3, [4]], $other);
        // The original array is left untouched.
        $this->assertSame([1], $array);
    }

    public function testSome() {
        $this->assertTrue(carr::some([null, 0, 'yes', false], function ($value) {
            return is_bool($value);
        }));
        $this->assertFalse(carr::some([1, 2, 3], function ($value) {
            return $value > 10;
        }));

        $users = [
            ['user' => 'barney', 'active' => true],
            ['user' => 'fred', 'active' => false],
        ];
        // The `property` iteratee shorthand.
        $this->assertTrue(carr::some($users, 'active'));
        $this->assertFalse(carr::some([['active' => false]], 'active'));
    }

    public function testEach() {
        $sum = 0;
        $result = carr::each([1, 2, 3], function ($value) use (&$sum) {
            $sum += $value;
        });
        $this->assertSame(6, $sum);
        $this->assertSame([1, 2, 3], $result);

        // Returning false from the iteratee stops iteration early.
        $visited = [];
        carr::each([1, 2, 3], function ($value) use (&$visited) {
            $visited[] = $value;
            if ($value === 2) {
                return false;
            }
        });
        $this->assertSame([1, 2], $visited);
    }
}
