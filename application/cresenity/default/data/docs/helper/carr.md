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


# title
`carr::add` method adds a given key / value pair to an array if the given key doesn't already exist in the array or is set to null:
