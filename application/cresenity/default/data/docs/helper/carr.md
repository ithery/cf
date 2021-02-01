# Helper carr

### Available Methods
[carr::accessible](#carr::accessible)

[carr::get](#title)



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
