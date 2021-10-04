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
