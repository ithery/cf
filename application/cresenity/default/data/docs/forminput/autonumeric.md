# Form Input - AutoNumeric

Numeric input that formats the value as it is typed — thousands separators, a fixed number of
decimal places, and optional bounds.

---

### Basic Usage

```php
$form->addField()->setLabel('Price')->addAutoNumericControl('price');
```

### Decimals and separators

```php
$form->addField()->setLabel('Price')->addAutoNumericControl('price')
    ->setDecimalDigit(2)
    ->setThousandSeparator('.')
    ->setDecimalSeparator(',');
```

| Method | Default | Purpose |
|---|---|---|
| `setDecimalDigit()` | `0` | number of decimal places |
| `setThousandSeparator()` | `,` | character between thousands |
| `setDecimalSeparator()` | `.` | character before the decimal part |

For Indonesian formatting the two separators are swapped, giving `1.234.567,89`:

```php
$form->addField()->setLabel('Harga')->addAutoNumericControl('price')
    ->setDecimalDigit(2)
    ->setThousandSeparator('.')
    ->setDecimalSeparator(',');
```

### Bounds

```php
$form->addField()->setLabel('Discount')->addAutoNumericControl('discount')
    ->setMinValue(0)
    ->setMaxValue(100);
```

Both bounds are optional and unset by default. They constrain what can be typed into the
control; they are not a substitute for validating the submitted value on the server.

### Reading the value

The control submits the formatted string, including its separators. Strip the formatting before
storing it:

```php
$post = $_POST;
$price = ctransform::unformat_currency(carr::get($post, 'price'));
```

Storing the formatted string directly is the most common mistake with this control — the column
then holds `1.234.567,89` rather than a number, and arithmetic on it silently truncates at the
first separator.
