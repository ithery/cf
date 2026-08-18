# Form Input - Date Time

Enhanced date and time picker controls beyond the standard HTML5 date input.

---

### Date

```php
$form->addField()->setLabel('Birth Date')->addDateControl('birth_date');
```

The accepted range and the display format can be constrained:

```php
$form->addField()->setLabel('Birth Date')->addDateControl('birth_date')
    ->setStartDate('1900-01-01')
    ->setEndDate(date('Y-m-d'))
    ->setDateFormat('dd-mm-yyyy');
```

- `setStartDate()` — earliest selectable date
- `setEndDate()` — latest selectable date
- `setDateFormat()` — the display format shown in the input

`setEndDate(date('Y-m-d'))` is the usual way to prevent a future date being chosen for
something that must already have happened.

### Time

```php
$form->addField()->setLabel('Start Time')->addTimeControl('start_time');
```

### Date Range

A pair of dates submitted as one field:

```php
$form->addField()->setLabel('Period')->addDateRangeControl('period')
    ->setValueStart('2026-01-01')
    ->setValueEnd('2026-12-31');
```

- `setValueStart()` / `setValueEnd()` — the initial range
- `setHaveButton()` — show apply and cancel buttons instead of applying on selection

### Date Time Modal

Date and time picker displayed in a modal dialog:

```php
$form->addField()->setLabel('Event Date')->addDateTimeModalControl('event_date');
```

### Date Time Material

Material design styled date-time picker:

```php
$form->addField()->setLabel('Appointment')->addDateTimeMaterialControl('appointment');
```

### Date Range Dropdown

Date range picker with preset options (Today, Last 7 Days, This Month, etc.):

```php
$form->addField()->setLabel('Period')->addDateRangeDropdownButtonControl('period');
```

## Storing the value

These controls submit the date in the display format they were configured with, which is not
necessarily the format the database expects. Convert before storing:

```php
$post = $_POST;
$date = ctransform::unformat_date(carr::get($post, 'birth_date'));
```
