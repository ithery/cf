# Module - Period

The `CPeriod` class represents a date/time range and provides utilities for working with periods, checking overlaps, and managing opening hours.

---

### Creating Periods

```php
$period = CPeriod::create('2024-01-01', '2024-02-01');
$oneMonth = CPeriod::months(1);
$thisMonth = CPeriod::thisMonth();
$today = CPeriod::today();
$yesterday = CPeriod::yesterday();
$last7Days = CPeriod::last7Days();
$last30Days = CPeriod::last30Days();
$thisWeek = CPeriod::thisWeek();
$lastWeek = CPeriod::lastWeek();
```

### Duration-Based

```php
$twoHours = CPeriod::hours(2);
$fiveDays = CPeriod::days(5);
$threeMonths = CPeriod::months(3);
$twoYears = CPeriod::years(2);
$tenMinutes = CPeriod::minutes(10);
```

---

### Check Overlaps

```php
$a = CPeriod::create('2024-01-01', '2024-02-01');
$b = CPeriod::create('2024-02-01', '2024-02-28');

$a->overlapsWith($b); // true
```

### Period Length

```php
$a = CPeriod::create('2024-01-01', '2024-01-31');
$a->length(); // 31
```

### Boundaries

Control whether start/end dates are included or excluded:

```php
$a = CPeriod::create('2024-01-01', '2024-02-01', CPeriod_Precision::DAY(), CPeriod_Boundaries::EXCLUDE_END());
$b = CPeriod::create('2024-02-01', '2024-02-28', CPeriod_Precision::DAY(), CPeriod_Boundaries::EXCLUDE_END());

$a->overlapsWith($b); // false (end of $a excluded)
```

### Iterating Over a Period

```php
$period = CPeriod::create(CCarbon::make('2024-01-01'), CCarbon::make('2024-01-31'));

foreach ($period as $date) {
    /** @var DateTimeImmutable $date */
    echo $date->format('Y-m-d');
}
```

---

### Opening Hours

Define business hours with exceptions for holidays:

```php
$data = [
    'monday' => ['09:00-12:00', '13:00-18:00'],
    'tuesday' => ['09:00-12:00', '13:00-18:00'],
    'wednesday' => ['09:00-12:00'],
    'thursday' => ['09:00-12:00', '13:00-18:00'],
    'friday' => ['09:00-12:00', '13:00-20:00'],
    'saturday' => ['09:00-12:00', '13:00-16:00'],
    'sunday' => [],
    'exceptions' => [
        '2024-12-25' => [],
        '01-01' => [],
        '12-25' => ['09:00-12:00'],
    ],
];
$openingHours = CPeriod::openingHours($data);
```

#### Checking Status

```php
$openingHours->isOpenOn('monday');     // true
$openingHours->isOpenOn('sunday');     // false
$openingHours->isOpenAt(new DateTime('2024-09-26 19:00:00')); // false
$openingHours->isOpenOn('2024-12-25'); // false (exception)
```

#### Current Open Range

```php
$now = new DateTime('now');
$range = $openingHours->currentOpenRange($now);
if ($range) {
    echo 'Open since ' . $range->start();
    echo 'Closes at ' . $range->end();
} else {
    echo 'Closed since ' . $openingHours->previousClose($now)->format('l H:i');
    echo 'Opens at ' . $openingHours->nextOpen($now)->format('l H:i');
}
```

#### Schedule Data

```php
$openingHours->forDay('monday');         // OpeningHoursForDay for Monday
$openingHours->forWeek();                // All days' schedules
$openingHours->forWeekCombined();        // Days grouped by same schedule
$openingHours->forDate(new DateTime());  // Schedule for specific date
$openingHours->exceptions();             // All exception dates
```
