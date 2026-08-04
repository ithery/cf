# Helper cnum

The `cnum` helper class formats numbers according to a locale — thousands separators,
currency, percentages, file sizes, and spelled-out numbers.

> **Requires the `intl` PHP extension.** Most methods wrap `NumberFormatter` and throw a
> `RuntimeException` when the extension is missing. `cnum::fileSize`, `cnum::abbreviate`,
> `cnum::forHumans`, and `cnum::clamp` are the exceptions and work without it.

### cnum::format

The `cnum::format` method formats a number with a thousands separator:

```php
$number = cnum::format(1234567);

// 1,234,567

$number = cnum::format(1234.5678, 2);

// 1,234.57

$number = cnum::format(1234567, null, null, 'id');

// 1.234.567
```

The third argument sets a maximum number of decimal places instead of a fixed one, so
trailing zeros are omitted:

```php
$number = cnum::format(1234.5, null, 2);

// 1,234.5
```

### cnum::currency

The `cnum::currency` method formats a number as currency:

```php
$money = cnum::currency(1234.56);

// $1,234.56

$money = cnum::currency(1234.56, 'IDR', 'id');

// Rp 1.234,56
```

### cnum::percentage

The `cnum::percentage` method formats a number as a percentage:

```php
$percentage = cnum::percentage(75);

// 75%

$percentage = cnum::percentage(75.456, 2);

// 75.46%
```

### cnum::spell

The `cnum::spell` method returns the number written out in words:

```php
$words = cnum::spell(102);

// one hundred two

$words = cnum::spell(102, 'id');

// seratus dua
```

The `$after` and `$until` arguments limit the range that is spelled out. Numbers outside the
range are formatted normally:

```php
cnum::spell(9, null, null, 10);

// nine

cnum::spell(1284, null, null, 10);

// 1,284
```

### cnum::ordinal

The `cnum::ordinal` method returns the ordinal form of a number:

```php
cnum::ordinal(1);    // 1st
cnum::ordinal(2);    // 2nd
cnum::ordinal(21);   // 21st
```

### cnum::fileSize

The `cnum::fileSize` method converts a byte count into a file size:

```php
$size = cnum::fileSize(1024);

// 1 KB

$size = cnum::fileSize(1024 * 1024, 2);

// 1.00 MB
```

### cnum::abbreviate

The `cnum::abbreviate` method shortens a large number using a single-letter suffix:

```php
cnum::abbreviate(1200);

// 1K

cnum::abbreviate(1234567, 2);

// 1.23M
```

### cnum::forHumans

The `cnum::forHumans` method shortens a large number using a word suffix:

```php
cnum::forHumans(1200);

// 1 thousand

cnum::forHumans(1234567, 2);

// 1.23 million
```

### cnum::clamp

The `cnum::clamp` method constrains a number to a given range:

```php
cnum::clamp(105, 10, 100);   // 100
cnum::clamp(5, 10, 100);     // 10
cnum::clamp(50, 10, 100);    // 50
```

### cnum::withLocale

The `cnum::withLocale` method executes a callback using the given locale and restores the
previous locale afterwards, including when the callback throws:

```php
$result = cnum::withLocale('id', function () {
    return cnum::currency(1234.56, 'IDR');
});

// Rp 1.234,56
```

### cnum::useLocale

The `cnum::useLocale` method changes the default locale for the remainder of the request:

```php
cnum::useLocale('id');
```

Prefer `cnum::withLocale` when the change is only needed for a single operation.
