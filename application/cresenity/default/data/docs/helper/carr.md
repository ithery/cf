# Helper carr

### carr::accessible

`carr::accessible` method dapat menentukan apakah value adalah array accessible:
```php
$isAccessible = carr::accessible(['a' => 1, 'b' => 2]);

// true

$isAccessible = carr::accessible(new CCollection);

// true

$isAccessible = carr::accessible('abc');

// false

$isAccessible = carr::accessible(new stdClass);
```


### carr::get
`carr::get` method untuk mendapatkan value dari suatu array.
value yang didapatkan bisa bersifat nested dengan menggunakan dot notation

```php
$array = ['products' => ['desk' => ['price' => 100]]];

$price = carr::get($array, 'products.desk.price');
```
The `carr::get` juga dapat menerima default value pada parameter ketiga yang akan dikembalikan jika key tidak ditemukan pada array:
```php
$discount = carr::get($array, 'products.desk.discount', 0);

// 0
```
### carr::add
`carr::add` method menambahkan key/value pair ke array walaupun key tidak tersedia ataupun berisi null

```php
$array = carr::add(['name' => 'Desk'], 'price', 100);

// ['name' => 'Desk', 'price' => 100]

$array = carr::add(['name' => 'Desk', 'price' => null], 'price', 100);

// ['name' => 'Desk', 'price' => 100]
```

### carr::collapse
`carr::collapse` method membuat multidimesi menjadi array satu dimensi

```php
$array = carr::collapse([[1, 2, 3], [4, 5, 6], [7, 8, 9]]);

// [1, 2, 3, 4, 5, 6, 7, 8, 9]
```

### carr::crossJoin
crr::crossJoin method melakukan cross joins dari 2 array,  dan akan menghasilkan Cartesian product dengan semua kemungkinan permutasi

```php
$matrix = carr::crossJoin([1, 2], ['a', 'b']);

/*
    [
        [1, 'a'],
        [1, 'b'],
        [2, 'a'],
        [2, 'b'],
    ]
*/

$matrix = carr::crossJoin([1, 2], ['a', 'b'], ['I', 'II']);

/*
    [
        [1, 'a', 'I'],
        [1, 'a', 'II'],
        [1, 'b', 'I'],
        [1, 'b', 'II'],
        [2, 'a', 'I'],
        [2, 'a', 'II'],
        [2, 'b', 'I'],
        [2, 'b', 'II'],
    ]
*/

```

### carr::divide
carr::divide method menghasilkan 2 array: 1 berisi keys array dan 1 lagi berisi values array

```php
[$keys, $values] = carr::divide(['name' => 'Desk']);

// $keys: ['name']

// $values: ['Desk']

/**
 * Use below statement when on the php 5.xx
 */
list($keys, $values) = carr::divide(['name' => 'Desk']);
```

### carr::dot
carr::dot method merubah multi-dimensional menjadi single array dengan "dot" notation untuk mengindikasikan kedalaman array

```php
$array = ['products' => ['desk' => ['price' => 100]]];

$flattened = carr::dot($array);

// ['products.desk.price' => 100]

```

### carr::except

carr::except method membuang key/value pair dari parameter yang dipassing ke suatu array

```php
$array = ['name' => 'Desk', 'price' => 100];

$filtered = carr::except($array, ['price']);

// ['name' => 'Desk']


```


### carr::exists
carr::exists method melakukan pengecheckan apakah key yang dipassing terpada pada array yang diberikan
```php

$array = ['name' => 'John Doe', 'age' => 17];

$exists = carr::exists($array, 'name');

// true

$exists = carr::exists($array, 'salary');

// false

```
### carr::first
The `Arr::first` method returns the first element of an array passing a given truth test:
```php

$array = [100, 200, 300];

$first = carr::first($array, function ($value, $key) {
    return $value >= 150;
});

// 200
```
A default value may also be passed as the third parameter to the method. This value will be returned if no value passes the truth test:
```php

$first = carr::first($array, $callback, $default);

```

### carr::flatten
The `carr::flatten` method flattens a multi-dimensional array into a single level array:
```php

$array = ['name' => 'Joe', 'languages' => ['PHP', 'Ruby']];

$flattened = carr::flatten($array);

// ['Joe', 'PHP', 'Ruby']
```

### carr::forget

The `carr::forget` method removes a given key / value pair from a deeply nested array using "dot" notation:
```php

$array = ['products' => ['desk' => ['price' => 100]]];

carr::forget($array, 'products.desk');

// ['products' => []]
```

### carr::has
The `carr::has` method checks whether a given item or items exists in an array using "dot" notation:
```php
$array = ['product' => ['name' => 'Desk', 'price' => 100]];

$contains = carr::has($array, 'product.name');

// true

$contains = carr::has($array, ['product.price', 'product.discount']);

// false
```

### carr::hasAny
The `carr::hasAny` method checks whether any item in a given set exists in an array using "dot" notation:
```php

$array = ['product' => ['name' => 'Desk', 'price' => 100]];

$contains = carr::hasAny($array, 'product.name');

// true

$contains = carr::hasAny($array, ['product.name', 'product.discount']);

// true

$contains = carr::hasAny($array, ['category', 'product.discount']);

// false
```

### carr::isAssoc
The `carr::isAssoc` method returns `true` if the given array is an associative array. An array is considered "associative" if it doesn't have sequential numerical keys beginning with zero:
```php
$isAssoc = carr::isAssoc(['product' => ['name' => 'Desk', 'price' => 100]]);

// true

$isAssoc = carr::isAssoc([1, 2, 3]);

// false
```

### carr::isList
The `carr::isList` method returns `true` if the given array's keys are sequential integers beginning from zero:
```php

    $isList = Arr::isList(['foo', 'bar', 'baz']);

    // true

    $isList = Arr::isList(['product' => ['name' => 'Desk', 'price' => 100]]);

    // false
```


### carr::join
The `carr::join` method joins array elements with a string. Using this method's second argument, you may also specify the joining string for the final element of the array:
```php

    $array = ['Tailwind', 'Alpine', 'Laravel', 'Livewire'];

    $joined = carr::join($array, ', ');

    // Tailwind, Alpine, Laravel, Livewire

    $joined = carr::join($array, ', ', ' and ');

    // Tailwind, Alpine, Laravel and Livewire
```
### carr::keyBy

The `carr::keyBy` method keys the array by the given key. If multiple items have the same key, only the last one will appear in the new array:

```php
    $array = [
        ['product_id' => 'prod-100', 'name' => 'Desk'],
        ['product_id' => 'prod-200', 'name' => 'Chair'],
    ];

    $keyed = carr::keyBy($array, 'product_id');

    /*
        [
            'prod-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
            'prod-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
        ]
    */
```

### carr::last

The `carr::last` method returns the last element of an array passing a given truth test:
```php
$array = [100, 200, 300, 110];

$last = carr::last($array, function ($value, $key) {
    return $value >= 150;
});

// 300
```
A default value may be passed as the third argument to the method. This value will be returned if no value passes the truth test:
```php
$last = carr::last($array, $callback, $default);
```

### carr::map

The `carr::map` method iterates through the array and passes each value and key to the given callback. The array value is replaced by the value returned by the callback:
```php
$array = ['first' => 'james', 'last' => 'kirk'];

$mapped = carr::map($array, function ($value, $key) {
    return ucfirst($value);
});

// ['first' => 'James', 'last' => 'Kirk']
```

### carr::only
The `carr::only` method returns only the specified key / value pairs from the given array:
```php

$array = ['name' => 'Desk', 'price' => 100, 'orders' => 10];

$slice = carr::only($array, ['name', 'price']);

// ['name' => 'Desk', 'price' => 100]

```

### carr::pluck
The `carr::pluck` method retrieves all of the values for a given key from an array:
```php
    $array = [
        ['product' => ['id' => 1, 'name' => 'Desk']],
        ['product' => ['id' => 2, 'name' => 'Chair']],
    ];

    $names = carr::pluck($array, 'product.name');

    // ['Desk', 'Chair']
```
You may also specify how you wish the resulting list to be keyed:
```php
    $names = carr::pluck($array, 'product.name', 'product.id');

    // [1 => 'Desk', 2 => 'Chair']
```

### carr::prepend
The `carr::prepend` method will push an item onto the beginning of an array:
```php

$array = ['one', 'two', 'three', 'four'];

$array = carr::prepend($array, 'zero');

// ['zero', 'one', 'two', 'three', 'four']
```
If needed, you may specify the key that should be used for the value:
```php

$array = ['price' => 100];

$array = carr::prepend($array, 'Desk', 'name');

// ['name' => 'Desk', 'price' => 100]
```

### carr::prependKeysWith

The `carr::prependKeysWith` prepends all key names of an associative array with the given prefix:
```php
$array = [
    'name' => 'Desk',
    'price' => 100,
];

$keyed = Arr::prependKeysWith($array, 'product.');

/*
    [
        'product.name' => 'Desk',
        'product.price' => 100,
    ]
*/

```

### carr::pull


The `carr::pull` method returns and removes a key / value pair from an array:
```php
$array = ['name' => 'Desk', 'price' => 100];

$name = carr::pull($array, 'name');

// $name: Desk

// $array: ['price' => 100]
```
A default value may be passed as the third argument to the method. This value will be returned if the key doesn't exist:
```php
$value = carr::pull($array, $key, $default);
```

### carr::query

The `carr::query` method converts the array into a query string:
```php
$array = [
    'name' => 'Desk',
    'order' => [
        'column' => 'created_at',
        'direction' => 'desc'
    ]
];

carr::query($array);

// name=Desk&order[column]=created_at&order[direction]=desc
```

### carr::random

The `carr::random` method returns a random value from an array:
```php

$array = [1, 2, 3, 4, 5];

$random = carr::random($array);

// 4 - (retrieved randomly)
```
You may also specify the number of items to return as an optional second argument. Note that providing this argument will return an array even if only one item is desired:
```php
$items = carr::random($array, 2);

// [2, 5] - (retrieved randomly)
```

### carr::set

The `carr::set` method sets a value within a deeply nested array using "dot" notation:
```php
$array = ['products' => ['desk' => ['price' => 100]]];

carr::set($array, 'products.desk.price', 200);

// ['products' => ['desk' => ['price' => 200]]]
```

### carr::shuffle

The `carr::shuffle` method randomly shuffles the items in the array:
```php
    $array = carr::shuffle([1, 2, 3, 4, 5]);

    // [3, 2, 5, 1, 4] - (generated randomly)
```

### carr::sort
The `carr::sort` method sorts an array by its values:
```php
$array = ['Desk', 'Table', 'Chair'];

$sorted = carr::sort($array);

// ['Chair', 'Desk', 'Table']
```

You may also sort the array by the results of a given closure:
```php

$array = [
    ['name' => 'Desk'],
    ['name' => 'Table'],
    ['name' => 'Chair'],
];

$sorted = array_values(carr::sort($array, function ($value) {
    return $value['name'];
}));

/*
    [
        ['name' => 'Chair'],
        ['name' => 'Desk'],
        ['name' => 'Table'],
    ]
*/
```

### carr::sortRecursive

The `carr::sortRecursive` method recursively sorts an array using the `sort` function for numerically indexed sub-arrays and the `ksort` function for associative sub-arrays:

```php
    $array = [
        ['Roman', 'Taylor', 'Li'],
        ['PHP', 'Ruby', 'JavaScript'],
        ['one' => 1, 'two' => 2, 'three' => 3],
    ];

    $sorted = carr::sortRecursive($array);

    /*
        [
            ['JavaScript', 'PHP', 'Ruby'],
            ['one' => 1, 'three' => 3, 'two' => 2],
            ['Li', 'Roman', 'Taylor'],
        ]
    */
```

### carr::toCssClasses

The `carr::toCssClasses` conditionally compiles a CSS class string. The method accepts an array of classes where the array key contains the class or classes you wish to add, while the value is a boolean expression. If the array element has a numeric key, it will always be included in the rendered class list:
```php
$isActive = false;
$hasError = true;

$array = ['p-4', 'font-bold' => $isActive, 'bg-red' => $hasError];

$classes = carr::toCssClasses($array);

/*
    'p-4 bg-red'
*/
```

### carr::undot()

The `carr::undot` method expands a single-dimensional array that uses "dot" notation into a multi-dimensional array:
```php
$array = [
    'product.type' => 'Desk',
    'product.location' => 'Bed Room',
];

$array = carr::undot($array);

// ['product' => ['type' => 'Desk', 'location' => 'Bed Room']]
```

### carr::where

The `carr::where` method filters an array using the given closure:
```php
$array = [100, '200', 300, '400', 500];

$filtered = carr::where($array, function ($value, $key) {
    return is_string($value);
});

// [1 => '200', 3 => '400']
```

### carr::whereNotNull

The `carr::whereNotNull` method removes all `null` values from the given array:
```php
    $array = [0, null];

    $filtered = carr::whereNotNull($array);

    // [0 => 0]
```

### carr::wrap

The `carr::wrap` method wraps the given value in an array. If the given value is already an array it will be returned without modification:
```php

    $string = 'Cresenity';

    $array = carr::wrap($string);

    // ['Cresenity']
```
If the given value is `null`, an empty array will be returned:
```php
    $array = carr::wrap(null);

    // []
```
