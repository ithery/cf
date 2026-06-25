# Form Input - Auto Numeric

Formatted number input with automatic thousand separators and decimal formatting. Useful for currency and numeric fields.

---

### Basic Usage

```php
$form->addField()->setLabel('Price')->addAutoNumericControl('price')
    ->setValue(50000);
```

### With Default Value

```php
$form->addField()->setLabel('Amount')->addAutoNumericControl('amount')
    ->setValue(1250000);
// Displays: 1,250,000
```
