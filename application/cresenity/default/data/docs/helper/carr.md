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
