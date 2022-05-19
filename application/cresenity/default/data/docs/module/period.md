# CPeriod

### Introduction

Contoh Kode:
```php
$period = new CPeriod('2021-01-01', '2021-02-01');

$oneMonth = CPeriod::month(1);

$thisMonth = CPeriod::thisMonth();

```


### Check Overlaps (>=1.4)
```php
$a = CPeriod::make('2021-01-01', '2021-02-01');
$b = CPeriod::make('2021-02-01', '2021-02-28');

cdbg::d($a->overlapsWith($b)); // true

$a = CPeriod::make('2021-01-01', '2021-01-31');
```
### Period Length (>=1.4)

```php
cdbg::d($a->length()); // 31

### Period Boundaries (>=1.4)
$a = CPeriod::make('2021-01-01', '2021-02-01', CPeriod_Precision::DAY(), CPeriod_Boundaries::EXCLUDE_END());
$b = CPeriod::make('2021-02-01', '2021-02-28', CPeriod_Precision::DAY(), CPeriod_Boundaries::EXCLUDE_END());

cdbg::d($a->overlapsWith($b)); // false
```

### Period Array Iterator (>=1.4)

```php
$datePeriod = CPeriod::make(CCarbon::make('2021-01-01'), CCarbon::make('2021-01-31'));

foreach ($datePeriod as $date) {
    /** @var DateTimeImmutable $date */
    cdbg::d($date);
}
```
